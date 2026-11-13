# Publish the reviewed local changes

Publication was blocked in the implementation session: local .git was read-only and GitHub's write connector required approval under a never-approve policy. No remote branch, commit or pull request was created.

Run these commands yourself from the repository root in a normal terminal after reviewing the files:

```sh
git status --short
git switch -c feat/hotel-portfolio-finalization
git diff --check
git add .github .gitignore README.md docs hotel-reservation/backend hotel-reservation/frontend/README.md
git diff --cached --stat
git diff --cached --check
git commit -m "Finalize secure hotel bookings and portfolio presentation"
git push -u origin feat/hotel-portfolio-finalization
gh pr create --base main --head feat/hotel-portfolio-finalization --title "Finalize secure hotel bookings and portfolio presentation" --body-file docs/PR_DESCRIPTION.md
```

GitHub CLI was not available in the implementation environment. If it is unavailable locally, open the pushed branch on GitHub and choose Compare & pull request. Paste PR_DESCRIPTION.md as the body.

The root ignore rules exclude local databases, caches, dependencies, .env files and generated build output. Inspect staged files before committing. Do not merge automatically, force-push, rewrite history, deploy or migrate an existing database.

After opening the PR, inspect the Application checks workflow. It has been prepared and its component commands passed locally; no hosted Actions run was performed during this session.
