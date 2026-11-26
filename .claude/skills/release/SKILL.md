---
name: release
description: Use this skill when the user wants to merge the dev branch into master and ship a release. Triggers include phrases like "release", "deploy", "ship it", "リリース", "デプロイ", "master にマージしてデプロイ", "本番リリース". Verifies a clean working tree, switches to master, fetches origin --prune, confirms master matches origin/master, merges dev with --no-ff using a commit message that names the upcoming version and links to its planned GitHub release page, drafts bilingual (Japanese then English) release notes from the changes since the previous release, then runs deploy.sh with that version, computed from the minor/patch bump confirmed up front (suggesting one from the changes since the previous release when the user did not specify it), and publishes a GitHub release for the new tag. Streams deployer output to the user and reports success or failure at the end.
version: 1.4.0
---

# release

Merge `dev` into `master`, run `deploy.sh` to publish the next release, and create the GitHub release page for the new version tag. The skill performs a hard-stop at any unexpected state — it never tries to "fix" the situation on its own.

## When to use

Invoke this skill when the user expresses any of the following intents:

- "Release", "deploy", "ship it", "publish a new version"
- "リリース", "デプロイ", "master にマージしてデプロイ", "本番リリース"
- Wanting to cut a new minor or patch release from the current `dev`

## Procedure

Run the steps below in order using the Bash tool. Proceed to the next step only if the previous one succeeds. If anything unexpected happens, **stop and report to the user — do not attempt recovery automatically**.

### Step 0: Confirm version bump

Decide whether this release is a `minor` or `patch` bump before touching any branch. Save the answer; it determines the new version used in the merge commit message and passed to `deploy.sh` later. This must happen up front so the long-running deploy is not blocked waiting for input later.

If the user already specified `minor` or `patch` in their request, use it as is and skip the rest of this step.

Otherwise, inspect the changes that will be released and suggest a bump. This is read-only: do not switch branches or fetch here. Use the local `dev`, since that is what Step 6 merges.

```bash
PREV_TAG=$(git tag -l 'v*' --sort=-v:refname | grep -m1 -E '^v[0-9]+\.[0-9]+\.[0-9]+')
git log --no-merges --format='--- %h %s%n%b' "$PREV_TAG"..dev
git diff --stat "$PREV_TAG"..dev
```

Read diffs when a commit subject alone does not tell you how visible the change is. Then classify, following the convention of past releases:

- **minor**: new user-facing features — a new page or statistic, a new kind of display element or significant information shown for the first time, a substantial rework of an existing page or feature, or a noticeable change in how statistics are calculated. A backward-compatible API addition (new endpoint or field) also counts as minor.
- **patch**: everything else — bug fixes, small UI tweaks to existing displays (tooltips, chart styling, formatting), translation fixes, adding data such as a new gear item, game version support data only (e.g. `Splatoon 3 vX.Y.Z` commits), dependency updates, internal-only changes (CI, linters, refactoring, tests, tooling).

If even one change qualifies as minor, suggest minor. If there are no commits at all since `PREV_TAG`, stop and tell the user there is nothing to release.

Ask with `AskUserQuestion`, putting the suggested option first with "(Recommended)" appended to its label. In each option's description, or in the question text, briefly state the reason (e.g. "新ページ entire/matching-range3 の追加を含むため" or "バグ修正と依存更新のみのため"), so the user can judge without reading the log.

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

### Step 5: Determine the previous and next release tags

Determine the latest existing version tag **before** `deploy.sh` creates the new one. Use the same selection rule as `deploy.sh` (`current_version`), but without running `deploy.sh` itself:

```bash
git tag -l 'v*' --sort=-v:refname | grep -m1 -E '^v[0-9]+\.[0-9]+\.[0-9]+'
```

Save it as `PREV_TAG`. It is the starting point of the release notes. If no tag is found, stop and report.

Then compute the next tag from `PREV_TAG` and the bump confirmed in Step 0, using the same rule as `deploy.sh`:

```bash
NEXT_TAG="v$(npx semver -i {minor|patch} "$PREV_TAG")"
```

Save it as `NEXT_TAG`. It must match `^v[0-9]+\.[0-9]+\.[0-9]+$`, and the tag must not already exist (`git rev-parse -q --verify "refs/tags/$NEXT_TAG"` prints nothing); otherwise stop and report.

### Step 6: Merge dev with --no-ff

```bash
REPO_URL=$(gh repo view --json url -q .url)
git merge --no-ff dev \
  -m "Merge branch 'dev' for $NEXT_TAG" \
  -m "Release notes: $REPO_URL/releases/tag/$NEXT_TAG"
```

The subject names the version this merge is released as, and the body links to the GitHub release page that Step 9 will publish for that tag. The page does not exist yet at merge time; the link is the planned URL. For example:

```
Merge branch 'dev' for v3.156.3

Release notes: https://github.com/fetus-hina/stat.ink/releases/tag/v3.156.3
```

If `REPO_URL` comes back empty, stop and report instead of merging.

If the merge fails (conflicts), stop and report to the user. Do not attempt to resolve conflicts automatically.

### Step 7: Draft the release notes

Draft the notes now (before the deploy) so they are ready the moment the deploy succeeds. Write them to a file in the scratchpad directory (e.g. `<scratchpad>/release-notes.md`) — never inside the repository.

#### 7-1. Gather the changes since `PREV_TAG`

```bash
# All non-merge commits with their bodies
git log --no-merges --format='--- %h %s%n%b' "$PREV_TAG"..HEAD

# Files touched, to spot user-facing and API-related areas
git diff --stat "$PREV_TAG"..HEAD

# API surface: check these in detail for compatibility impact
git diff "$PREV_TAG"..HEAD -- \
  actions/api controllers/Api*.php models/api models/Api*.php \
  components/formatters components/openapi API.md doc/
```

For `Update dependencies` commits, check the merged PR body (generated by `./yii deps/summarize-changes`, which contains a security audit section) with `gh pr view <number> --json body` to find out whether any notable security fix was included.

Read actual diffs whenever a commit subject alone does not tell you whether the change is user-visible.

#### 7-2. Decide what to include

Write for **stat.ink users and API client developers** (s3s, IkaLog, etc.), not for maintainers.

- **Splatoon support**: game version support (commits like `Splatoon 3 vX.Y.Z`), new weapons, stages, gear, seasons, etc. Link the Nintendo patch notes when the commit has a `Ref:` URL.
- **New features and behavior changes**: anything a user can see or that changes how pages, statistics, or the API behave (e.g. new display elements, changed sort orders, changed calculations).
- **API compatibility**: there should normally be none, but always state the result explicitly. If anything in the API surface changed (request/response fields, validation, status codes, endpoints, accepted values), explain what changed and whether it is, or could be, a breaking change for clients. If nothing changed, say so in one line.
- **Dependencies**: just say "依存ライブラリを更新しました" / "Updated dependencies". Only describe details when there is a significant security fix that affects stat.ink in practice, or a major architectural change (e.g. framework or runtime major upgrade, a new required server component).
- **Omit internal-only changes**: CI, linters, static analysis, refactoring, tests, documentation for developers, tooling configuration, and similar. Server requirement changes that matter to people self-hosting stat.ink (e.g. minimum PostgreSQL version) may be mentioned briefly under "その他 / Other".

Omit any section that has no entries, except the API section, which is always present.

#### 7-3. Format

Japanese first, then the same content in English. The first line announces that English follows.

```markdown
*English follows Japanese.*

## 変更内容

### Splatoon 3 対応
- Splatoon 3 v12.0.0 に対応しました（[更新内容](https://support.nintendo.com/jp/switch/software_support/av5ja/1200.html)）

### 新機能・仕様変更
- …

### API
- API の互換性に影響する変更はありません。

### その他
- 依存ライブラリを更新しました。

---

## Changes

### Splatoon 3 support
- Added support for Splatoon 3 v12.0.0 ([patch notes](https://support.nintendo.com/jp/switch/software_support/av5ja/1200.html))

### New features and changes
- …

### API
- No changes affecting API compatibility.

### Other
- Updated dependencies.
```

Keep entries short (one line each where possible), natural in each language, and avoid commit hashes or internal jargon. Show the drafted notes to the user in the conversation before running the deploy (no confirmation is required; this is just so they can see them).

### Step 8: Run deploy.sh

Invoke `deploy.sh` with the explicit `NEXT_TAG` from Step 5 (not `minor`/`patch`), so the created tag is guaranteed to match the version in the merge commit message. Use a long timeout (e.g. 600000ms = 10 minutes) since the deployer can take several minutes.

```bash
./deploy.sh "$NEXT_TAG"
```

**Streaming requirement:** The user wants to see deployer progress live, including the tag that gets created, the `git push`, and the `dep deploy` output. Do not truncate or summarize the output during the run — surface it as-is in the tool call.

On failure: report the failing step (composer install / git tag / git push / dep deploy) and the relevant error excerpt. Do **not** attempt to roll back or retry automatically, and do **not** create the GitHub release. Leave the user on `master` so the failed state can be inspected, and stop here.

### Step 9: Create the GitHub release (success only)

Use `NEXT_TAG` as `TAG` (confirm the deploy output shows that tag being created and pushed) and publish the release with the notes from Step 7:

```bash
gh release create "$TAG" --verify-tag --title "$TAG" --notes-file <scratchpad>/release-notes.md
```

`--verify-tag` ensures the release is attached to the pushed tag instead of silently creating a new one. If this fails, report the error and the command to retry, but do not block — the deploy itself already succeeded.

### Step 10: Switch back to dev (success only)

After a successful deploy, switch the working branch back to `dev` so the user is ready to keep developing.

```bash
git switch dev
```

If this fails for some reason, report it but do not block — the deploy itself already succeeded.

### Step 11: Report result

Report concisely that the release tag was created, pushed, and deployed, including the new tag name, and give the URL of the GitHub release page (from the `gh release create` output).

## Notes

- This skill performs a **production deploy**. Never use force flags, never skip hooks, never bypass signing.
- The merge into `master`, the deploy tag/push, and the GitHub release are outward-facing and not reversible without manual intervention — bail out at the first sign of trouble.
- Always confirm `minor` vs `patch` up front (Step 0) so the merge commit message can name the new version and the long-running deploy is not interrupted. When the user did not specify it, suggest one based on the changes, but let the user make the final choice.
- The deployer script creates an annotated tag (`git tag -a`), not a signed one. If tag creation fails, surface the error to the user and stop.
- Release notes are user-facing: describe changes from the user's point of view, and never include secrets, internal hostnames, or private details.
