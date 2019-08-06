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

This section guides you through submitting a bug report for Block Lab.  Following these guidelines helps maintainers and the community understand your report :pencil:, reproduce the behavior :computer: :computer:, and find related reports :mag_right:.

Before creating bug reports, please check [this list](#before-submitting-a-bug-report) as you might find out that you don't need to create one.  When you are creating a bug report, please [include as many details as possible](#how-do-i-submit-a-good-bug-report).

> **Note:** If you find a **Closed** issue that seems like it is the same thing that you're experiencing, open a new issue and include a link to the original issue in the body of your new one.

#### Before Submitting A Bug Report

* You might be able to find the cause of the problem and fix things yourself.  Most importantly, check if you can reproduce the problem [in the latest version of Block Lab](https://github.com/getblocklab/block-lab/releases).
* **Check the [FAQs](https://github.com/getblocklab/block-lab#frequently-asked-questions)** for a list of common questions and problems.
* While entering the title of your new issue, GitHub might show related issues below. If a related issue describes your problem and it's still open, add a comment to the existing issue instead of opening a new one.

#### How Do I Submit A (Good) Bug Report?

Bugs are tracked as [GitHub issues](https://guides.github.com/features/issues/) and ideally includes the following information by filling in [the template](ISSUE_TEMPLATE.md).

Explain the problem and include additional details to help maintainers reproduce the problem:

* **Use a clear and descriptive title** for the issue to identify the problem.
* **Describe the exact steps which reproduce the problem** in as many details as possible.  When listing steps, **don't just say what you did, but explain how you did it**.  For example, if you moved the cursor to the end of a line, explain if you used the mouse or the keyboard.
* **Provide specific examples to demonstrate the steps**.  Include links to files or GitHub projects, or copy/pasteable snippets, which you use in those examples.  If you're providing snippets in the issue, use [Markdown code blocks](https://help.github.com/articles/markdown-basics/#multiple-lines).
* **Describe the behavior you observed after following the steps** and point out what exactly is the problem with that behavior.
* **Explain which behavior you expected to see instead and why.**
* **Include screenshots and animated GIFs** which show you following the described steps and clearly demonstrate the problem.  You can use [this tool](https://www.cockos.com/licecap/) to record GIFs on macOS and Windows, and [this tool](https://github.com/colinkeenan/silentcast) or [this tool](https://github.com/GNOME/byzanz) on Linux.
* **If the problem wasn't triggered by a specific action**, describe what you were doing before the problem happened and share more information using the guidelines below.

Provide more context by answering these questions:

* **Did the problem start happening recently** (e.g. after updating to a new version of Block Lab) or was this always a problem?
* If the problem started happening recently, **can you reproduce the problem in an older version of Block Lab?**  What's the most recent version in which the problem doesn't happen?  You can download older versions of Block Lab from [the releases page](https://github.com/getblocklab/block-lab/releases).
* **Can you reliably reproduce the issue?**  If not, provide details about how often the problem happens and under which conditions it normally happens.

Include details about your configuration and environment:

* **Which version of Block Lab are you using?**
* **What version of WordPress are you using**?  What other software versions are in use?
* **Which themes and other plugins do you have installed?**

### Suggesting Enhancements

This section guides you through submitting an enhancement suggestion for Block Lab, including completely new features and minor improvements to existing functionality.  Following these guidelines helps maintainers and the community understand your suggestion :pencil: and find related suggestions :mag_right:.

Before creating enhancement suggestions, please check [this list](#before-submitting-an-enhancement-suggestion) as you might find out that you don't need to create one.  When you are creating an enhancement suggestion, please [include as many details as possible](#how-do-i-submit-a-good-enhancement-suggestion).  Fill in [the template](ISSUE_TEMPLATE.md), including the steps that you imagine you would take if the feature you're requesting existed.

#### Before Submitting An Enhancement Suggestion

While entering the title of your new issue, GitHub may show related issues. If a related issue captures your idea, add a comment to the existing issue instead of opening a new one.

#### How Do I Submit A (Good) Enhancement Suggestion?

Enhancement suggestions are tracked as [GitHub issues](https://guides.github.com/features/issues/) and ideally provides the following information:

* **Use a clear and descriptive title** for the issue to identify the suggestion.
* **Provide a step-by-step description of the suggested enhancement** in as many details as possible.
* **Provide specific examples to demonstrate the steps**.  Include copy/pasteable snippets which you use in those examples, as [Markdown code blocks](https://help.github.com/articles/markdown-basics/#multiple-lines).
* **Describe the current behavior** and **explain which behavior you expected to see instead** and why.
* **Include screenshots and animated GIFs** which help you demonstrate the steps or point out the part of Block Lab which the suggestion is related to.  You can use [this tool](https://www.cockos.com/licecap/) to record GIFs on macOS and Windows, and [this tool](https://github.com/colinkeenan/silentcast) or [this tool](https://github.com/GNOME/byzanz) on Linux.
* **Explain why this enhancement would be useful** to most Block Lab users.
* **Specify which version of Block Lab you're using.**
* **Specify the WordPress version you're using.**

### Your First Code Contribution

Unsure where to begin contributing to Block Lab?  You can start by looking through these `good-first-issue` and `help-wanted` issues:

* [New contributor issues][good-first-issue] - issues which should only require a few lines of code, and a test or two.
* [Help wanted issues][help-wanted] - issues which should be a bit more involved than `good-first-issue` issues.

Both issue lists are sorted by total number of comments.  While not perfect, number of comments is a reasonable proxy for impact a given change will have.

### Pull Requests

The process described here has several goals:

- Maintain Block Lab's quality
- Fix problems that are important to users
- Engage the community in working toward the best possible Block Lab
- Enable a sustainable system for Block Lab's maintainers to review contributions

Please follow these steps to have your contribution considered by the maintainers:

1. Follow all instructions in [the template](PULL_REQUEST_TEMPLATE.md)
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

Block Lab development requires Node.js and follows the WordPress coding standards for PHP and JavaScript. In order to get your development enviorment setup quickly, simply run the following commands after cloning the plugin from Github:

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

Also while developing, it is advantageous to install the Composer dependencies to ensure adherence to PHP WordPress coding standards.

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
