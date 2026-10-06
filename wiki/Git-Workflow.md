# Git Workflow

## Connect an existing local folder to the private GitHub repository

From the AWare Support Workbench repository root in the VS Code terminal:

```powershell
git init
git branch -M main
git remote add origin https://github.com/jocire/AWare-Support-Workbench.git
```

If `origin` already exists, verify it instead:

```powershell
git remote -v
```

If it points somewhere else:

```powershell
git remote set-url origin https://github.com/jocire/AWare-Support-Workbench.git
```

## Check what will be committed

```powershell
git status
git diff
```

Make sure `.env`, `node_modules/`, Playwright reports, test results, ZIPs and local logs are not staged. The repository `.gitignore` excludes these.

## First push to an empty repository

```powershell
git add .
git status
git commit -m "Initial AWare Support Workbench repository"
git push -u origin main
```

If the GitHub repository was created with an initial README/license/gitignore, it is not empty. Fetch first:

```powershell
git fetch origin
git log --oneline --all --decorate --graph -20
```

Then merge/rebase intentionally rather than forcing over remote history. If the remote only contains placeholder files and you want to retain them, normally:

```powershell
git pull --rebase origin main
git push -u origin main
```

Resolve any file conflicts before continuing.

## VS Code UI

You can do the same through **Source Control** in VS Code:

1. Open the repository folder.
2. Select the Source Control icon.
3. Review changed files individually.
4. Stage the files you intend to commit.
5. Enter a commit message and choose **Commit**.
6. Choose **Sync Changes** / **Push**.

For the first connection, VS Code may ask you to authenticate with GitHub. Use the GitHub account that owns or has access to the private repository.

## Before every push

Recommended minimum:

```powershell
npm run test:php
git status
git diff --check
```

For changes touching browser behavior:

```powershell
npm run test:browser
```

For Engineer Session, MU-bootstrap, authentication/session-binding, plugin-isolation, or release-candidate changes, run the complete release gate:

```powershell
npm test
```

Do not push credentials from `.env`.

## Wiki publishing

The `wiki/` directory is the canonical Wiki source kept together with the code. GitHub Wikis use a separate Git repository ending in `.wiki.git`.

After the GitHub Wiki has been enabled/initialized, publish with the repository helper:

```powershell
.\scripts\publish-wiki.ps1
```

Review the script before first use and verify it targets:

```text
https://github.com/jocire/AWare-Support-Workbench.wiki.git
```

Keeping Wiki source in the normal repository means documentation changes can be reviewed and versioned alongside code even though GitHub renders the Wiki from a separate repository.
