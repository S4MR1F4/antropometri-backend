# Agent Guidelines for Antropometri (AGENTS.md)

See [GEMINI.md](file:///d:/SAMRIFA/projects/antropometri/GEMINI.md) and [.agents/rules/engineering-standard.md](file:///d:/SAMRIFA/projects/antropometri/.agents/rules/engineering-standard.md) for full engineering rules.

### Core Principles:
1. **Strict Emoji Ban**: Dilarang menggunakan emoji mentah (👶, 🤰, ⚖️, 📱, dll.) pada UI maupun diagram Mermaid. Gunakan official developer icons (Material Icons / Cupertino / SVG).
2. **CodeGraph First**: Always explore symbols, call graphs, and impact radius via `codegraph` before and during edits.
3. **Zero Errors & Zero Warnings**: Enforce `flutter analyze` clean (0 issues) and 100% passing tests for both Flutter and Laravel (`php artisan test`).
4. **Round-Trip Excel Standard**: Export and import Excel formats must be 100% identical and bidirectional.
5. **Conventional Commits & Semantic Tagging**: Commit cleanly and use annotated git tags (`vX.Y.Z`) on releases.
6. **Outline SOP**: Maintain living docs on Outline (`https://outline.samrifa.com/`). Use temporary `[DEV-TASK]` documents for task planning to safeguard context against IDE/AI restarts, and delete them once released and pushed.
