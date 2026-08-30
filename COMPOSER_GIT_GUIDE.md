# Composer & Git Workflow Guide

This guide explains how to manage PHP dependencies (`vendor/`) with Composer and Git, preventing missing package errors when pulling changes or switching branches.

---

## 1. Core Principles

* **`vendor/` in `.gitignore`**: **Correct & Standard.** The `vendor/` folder contains third-party dependencies and should **never** be committed to Git.
* **`composer.json` & `composer.lock`**: These files define your exact project dependencies and version constraints. They **MUST ALWAYS** be committed together to Git.

---

## 2. Composer Command Reference

| Command | Purpose | When to Use |
| :--- | :--- | :--- |
| `sail composer require <package>` | Installs a **new package** and updates `composer.json` / `composer.lock`. | Only when adding a new package to the project. |
| `sail composer remove <package>` | Uninstalls a package and updates `composer.json` / `composer.lock`. | When removing an unused package. |
| `sail composer install` | Syncs your local `vendor/` folder to match `composer.lock`. | After `git pull`, `git checkout`, or cloning. |

---

## 3. When to run `sail composer install`

Run `sail composer install` in these 3 scenarios:

1. **After `git pull`**: Whenever you pull changes that updated `composer.lock`.
2. **After `git checkout`**: When switching between branches (`main` ↔ `develop`) if dependencies differ.
3. **Fresh Project Setup**: When cloning the repository on a new machine.

> **Note:** `composer install` reads `composer.lock` and uses local download cache. It takes only a few seconds and ensures your `vendor/` folder is never corrupted or missing packages.

---

## 4. Standard Daily Git & Composer Workflow

Follow this step-by-step workflow when adding features or packages:

### Step 1: Install Package & Commit on `develop`
```bash
# 1. Install package
sail composer require intervention/image-laravel

# 2. Stage changes (including composer.json and composer.lock)
git add .

# 3. Commit
git commit -m "feat: add intervention image package"

# 4. Push to GitHub
git push origin develop
```

### Step 2: Merge on GitHub
Create a Pull Request or merge `develop` into `main` directly on GitHub.

### Step 3: Update Local `main` & Sync `vendor/`
```bash
# 1. Switch to main
git checkout main

# 2. Pull latest merged main from GitHub
git pull origin main

# 3. SYNC VENDOR FOLDER (Prevents missing package errors)
sail composer install
```

### Step 4: Sync `develop` with `main`
```bash
# 1. Switch back to develop
git checkout develop

# 2. Merge main into develop to stay updated
git merge main
```

