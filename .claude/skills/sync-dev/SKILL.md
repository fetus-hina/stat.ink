---
name: sync-dev
description: Use this skill when the user wants to update the local dev branch to match origin/dev — fetch latest remote refs and fast-forward. Triggers include phrases like "sync dev", "update dev", "pull dev", "dev ブランチを最新に", "dev を更新", "リモートに追従". Performs switch to dev, fetch origin --prune, then merge --ff-only origin/dev. Notifies the user if fast-forward is not possible instead of attempting a non-FF merge.
version: 1.0.0
---

# sync-dev

Bring the local `dev` branch up to date with `origin/dev`. Only fast-forward updates are performed; anything else stops and reports to the user.

## When to use

Invoke this skill when the user expresses any of the following intents:

- "Sync dev", "update dev", "pull dev", "dev ブランチを最新にして", "dev を更新して"
- Wanting the local development branch to catch up with the remote
- Refreshing the base before starting new work

## Procedure

Run the steps below in order using the Bash tool. Proceed to the next step only if the previous one succeeds.

### Step 1: Check working-tree state

Verify there are no uncommitted changes.

```bash
git status --porcelain
```

If the output is non-empty (working tree is dirty), stop and report the situation to the user. Do **not** stash or discard changes on your own.

### Step 2: Switch to dev

```bash
git switch dev
```

This succeeds even when already on `dev`. If it fails (e.g. the `dev` branch does not exist), stop and notify the user.

### Step 3: Fetch from origin

```bash
git fetch origin --prune
```

`--prune` cleans up references to remote branches that have been deleted upstream.

### Step 4: Fast-forward merge

```bash
git merge --ff-only origin/dev
```

- **On success**: report concisely how many commits were pulled in, or that the branch was already up to date.
- **On failure (fast-forward not possible)**: do **not** attempt a non-FF merge, rebase, or reset. Stop and tell the user:
  - Fast-forward was not possible
  - Local `dev` has likely diverged from the remote
  - Suggest inspecting the situation with e.g. `git log --oneline --graph --decorate dev origin/dev`
  - Leave the resolution choice (rebase / merge / reset) to the user

## Notes

- This skill performs **read-only and fast-forward operations only**. It never uses force flags or `reset --hard`.
- Whenever the working tree is dirty or a fast-forward is not possible, defer to the user — never resolve it automatically.
- Keep the final report short (one or two sentences): how many commits advanced, or "Already up to date."
