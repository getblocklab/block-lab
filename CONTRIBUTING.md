# Contributing to Block Lab

:+1::tada: First off, thanks for taking the time to contribute! :tada::+1:

The following is a set of guidelines for contributing to Block Lab.  These are mostly guidelines, not rules.  Use your best judgment, and feel free to propose changes to this document in a pull request.

#### Table Of Contents

[Code of Conduct](#code-of-conduct)

[How Can I Contribute?](#how-can-i-contribute)
  * [Reporting Bugs](#reporting-bugs)
  * [Suggesting Enhancements](#suggesting-enhancements)
  * [Your First Code Contribution](#your-first-code-contribution)
  * [Pull Requests](#pull-requests)
  * [Local Setup](#local-setup)

[Styleguides](#styleguides)
  * [Git Commit Messages](#git-commit-messages)

[Additional Notes](#additional-notes)
  * [Issue and Pull Request Labels](#issue-and-pull-request-labels)

## Code of Conduct

This project and everyone participating in it is governed by the [Block Lab Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code.  Please report unacceptable behavior to [info@getblocklab.com](mailto:info@getblocklab.com).

## How Can I Contribute?

### Reporting Bugs

Bugs are tracked as [GitHub issues](https://guides.github.com/features/issues/).

Explain the problem and include additional details to help maintainers reproduce the problem.

### Suggesting Enhancements

Enhancement suggestions are also tracked as [GitHub issues](https://guides.github.com/features/issues/).

Please describe in detail the enhancement and your use case for it.

### Your First Code Contribution

### Pull Requests

Please follow these steps to have your contribution considered by the maintainers:

1. Follow all instructions in [the template](.github/PULL_REQUEST_TEMPLATE.md)
2. Follow the [styleguides](#styleguides)
3. After you submit your pull request, verify that all [status checks](https://help.github.com/articles/about-status-checks/) are passing <details><summary>What if the status checks are failing?</summary>If a status check is failing, and you believe that the failure is unrelated to your change, please leave a comment on the pull request explaining why you believe the failure is unrelated.  A maintainer will re-run the status check for you.  If we conclude that the failure was a false positive, then we will open an issue to track that problem with our status check suite.</details>

While the prerequisites above must be satisfied prior to having your pull request reviewed, the reviewer(s) may ask you to complete additional design work, tests, or other changes before your pull request can be ultimately accepted.

## Styleguides

### Git Commit Messages

* Use the present tense ("Add feature" not "Added feature")
* Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
* Limit the first line to 72 characters or less
* Reference issues and pull requests liberally after the first line
* Consider starting the commit message with an applicable emoji:
    * :art: `:art:` when improving the format/structure of the code
    * :racehorse: `:racehorse:` when improving performance
    * :non-potable_water: `:non-potable_water:` when plugging memory leaks
    * :memo: `:memo:` when writing docs
    * :penguin: `:penguin:` when fixing something on Linux
    * :apple: `:apple:` when fixing something on macOS
    * :checkered_flag: `:checkered_flag:` when fixing something on Windows
    * :bug: `:bug:` when fixing a bug
    * :fire: `:fire:` when removing code or files
    * :white_check_mark: `:white_check_mark:` when adding tests
    * :lock: `:lock:` when dealing with security

### Local Setup

Block Lab development requires Node.js and follows the WordPress coding standards for PHP and JavaScript. In order to get your development environment setup quickly, simply run the following commands after cloning the plugin from Github:

#### Node

**Install Packages**

```
npm install
```

This command installs required Node packages locally and is required before running build setup.

**Watch Changes**

```
npm run dev
```

While developing, it is a best practice to watch for changes. This command will build assets as modifications are made.

#### Composer

```
composer install
```

## Additional Notes

### Issue and Pull Request Labels

This section lists the labels we use to help us track and manage issues and pull requests.  Please open an issue if you have suggestions for new labels.

[GitHub search](https://help.github.com/articles/searching-issues/) makes it easy to use labels for finding groups of issues or pull requests you're interested in.  For example, you might be interested in [open issues which are labeled as bugs and are good for new contributors](https://github.com/getblocklab/block-lab/issues?q=is%3Aopen+is%3Aissue+label%3Abug+label%3A%22good+first+issue%22) or perhaps [open pull requests which haven't been reviewed yet](https://github.com/getblocklab/block-lab/pulls?q=is%3Apr+is%3Aopen+review%3Anone).  To help you find issues and pull requests, each label is listed with search links for finding open items with that label.  We  encourage you to read about [other search filters](https://help.github.com/articles/searching-issues/) which will help you write more focused queries.

#### Issue and Pull Request Labels

| Label name | `getblocklab/block-lab` :mag_right: | Description |
| --- | --- | --- |
| `bug` | [search][search-block-lab-repo-label-bug] | Something isn't working. |
| `control` | [search][search-block-lab-repo-label-control] |  |
| `duplicate` | [search][search-block-lab-repo-label-duplicate] | This issue or pull request already exists. |
| `enhancement` | [search][search-block-lab-repo-label-enhancement] | New feature or request. |
| `good-first-issue` | [search][search-block-lab-repo-label-good-first-issue] | Good for newcomers. |
| `help-wanted` | [search][search-block-lab-repo-label-help-wanted] | Extra attention is needed. |
| `invalid` | [search][search-block-lab-repo-label-invalid] | This doesn't seem right. |
| `question` | [search][search-block-lab-repo-label-question] | Further information is requested. |
| `wontfix` | [search][search-block-lab-repo-label-wontfix] | This will not be worked on. |

Thanks! :heart: :heart: :heart:

Block Lab Team
