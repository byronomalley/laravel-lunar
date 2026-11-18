# Project Overview
- Application: [purpose and main domain].
- Stack: Laravel [version], PHP [version], database [engine/version].
- Tests: [installed runner/version].
- Relevant documentation: [maintained repository-relative paths].

# Architecture
- Follow comparable implementations in [paths].
- Organization/tenant rules: [documented source].
- Use existing layers; do not introduce services/actions by default.

# Change Rules
- Inspect first; propose a plan for every nontrivial task.
- Wait for plan and acceptance-criteria approval before implementation.
- Stay within scope; stop when assumptions prove false.
- Obtain explicit approval for schema, dependency, public API,
  authentication, authorization, or deployment changes.
- Do not read, edit, or expose secret-bearing files.
- Never access production systems or databases.
- These instructions require supporting permission controls.

# Laravel Rules
- Follow existing request, controller, policy, and resource patterns.
- Validate input and select writable attributes explicitly.
- Derive ownership from trusted, authorized server-side context.
- Preserve tenant boundaries and existing response conventions.
- Add success and relevant failure-path feature tests.

# Validation
- Confirm an isolated test database before running tests.
- Targeted tests: [approved command].
- Wider tests: [approved command].
- Style: [configured command, or unavailable].
- Static analysis: [configured command, or unavailable].
- Do not install tools or weaken checks without approval.

# Final Report
- List changed files and behavior changes.
- Record commands and actual results.
- Distinguish passed, failed, skipped, and unavailable checks.
- Disclose baseline failures, assumptions, and manual follow-up.
