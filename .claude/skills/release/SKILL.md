---
name: release
description: Use this skill when the user wants to merge the dev branch into master and ship a release. Triggers include phrases like "release", "deploy", "ship it", "リリース", "デプロイ", "master にマージしてデプロイ", "本番リリース". Verifies a clean working tree, switches to master, fetches origin --prune, confirms master matches origin/master, merges dev with --no-ff, then runs deploy.sh with the minor/patch bump confirmed up front. Streams deployer output to the user and reports success or failure at the end.
version: 1.0.0
---

# release

Merge `dev` into `master` and run `deploy.sh` to publish the next release. The skill performs a hard-stop at any unexpected state — it never tries to "fix" the situation on its own.

## When to use

Invoke this skill when the user expresses any of the following intents:

- "Release", "deploy", "ship it", "publish a new version"
- "リリース", "デプロイ", "master にマージしてデプロイ", "本番リリース"
- Wanting to cut a new minor or patch release from the current `dev`

## Procedure

Run the steps below in order using the Bash tool. Proceed to the next step only if the previous one succeeds. If anything unexpected happens, **stop and report to the user — do not attempt recovery automatically**.

### Step 0: Confirm version bump

Before touching git, ask the user whether this release is a `minor` or `patch` bump. Use `AskUserQuestion` with these two options. Save the answer; it will be passed to `deploy.sh` in the final step.

This must happen up front so the long-running deploy is not blocked waiting for input later.

### Step 1: Check working-tree state

```bash
git status --porcelain
```

If the output is non-empty (working tree is dirty), stop and report the situation to the user. Do **not** stash or discard changes on your own.

### Step 2: Switch to master

```bash
git switch master
```

If the switch fails (e.g. master does not exist locally), stop and notify the user.

### Step 3: Fetch from origin

```bash
git fetch origin --prune
```

### Step 4: Verify master matches origin/master

```bash
git rev-parse HEAD origin/master
```

If the two SHAs do not match, local `master` has diverged from the remote. **Stop** and tell the user — do not rebase, reset, or force-push. Suggest the user inspect with `git log --oneline --graph --decorate master origin/master`.

### Step 5: Merge dev with --no-ff

```bash
git merge --no-ff dev -m "Merge branch 'dev'"
```

If the merge fails (conflicts), stop and report to the user. Do not attempt to resolve conflicts automatically.

### Step 6: Run deploy.sh

Invoke `deploy.sh` with the bump confirmed in Step 0. Use a long timeout (e.g. 600000ms = 10 minutes) since the deployer can take several minutes.

```bash
./deploy.sh {minor|patch}
```

**Streaming requirement:** The user wants to see deployer progress live, including the tag that gets created, the `git push`, and the `dep deploy` output. Do not truncate or summarize the output during the run — surface it as-is in the tool call.

### Step 7: Report result

- **On success**: report concisely that the release tag was created, pushed, and deployed. Include the new tag name from the output.
- **On failure**: report the failing step (composer install / git tag / git push / dep deploy) and the relevant error excerpt. Do **not** attempt to roll back or retry automatically. Leave recovery decisions to the user. **Skip Step 8** — leave the user on `master` so the failed state can be inspected.

### Step 8: Switch back to dev (success only)

After a successful deploy, switch the working branch back to `dev` so the user is ready to keep developing.

```bash
git switch dev
```

If this fails for some reason, report it but do not block — the deploy itself already succeeded.

## Notes

- This skill performs a **production deploy**. Never use force flags, never skip hooks, never bypass signing.
- The merge into `master` and the deploy tag/push are not reversible without manual intervention — bail out at the first sign of trouble.
- Always confirm `minor` vs `patch` up front (Step 0) so the long-running deploy is not interrupted.
- The deployer script itself signs the tag (`git tag -s`); this requires the user's GPG setup to be ready. If signing fails, surface the error to the user and stop.
