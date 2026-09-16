# CSS Token 消费率审计

生成时间：Phase 0.1
审计范围：assets/css/ 全部文件（不含 tokens.css 本身）

## 结论

| 文件 | 硬编码项 | 状态 |
|---|---|---|
| main.css | 42 处 | 需收敛 |
| mobile.css | 18 处 | 可接受（断点覆盖） |
| motion.css | 3 处 | 可接受（动效参数） |
| reader.css | 4 处 | 已收敛 |
| reset.css | 0 | 合规 |
| tokens.css | — | 唯一源 |

## main.css 硬编码清单

### A 类 · 必须 Token 化（30 处）

| 位置 | 当前值 | 应替换为 |
|---|---|---|
| .theme-toggle | border-radius: 5px | var(--seed-radius-sm) |
| .nav-cta | border-radius: 5px | var(--seed-radius-sm) |
| .nav-cta:hover | rgba(0,255,136,.35) | var(--map-green-shadow-md) |
| .side-dot::before | border-radius: 4px | var(--seed-radius-sm) |
| .float-cta | border-radius: 6px | var(--seed-radius) |
| .float-cta | rgba(0,255,136,.15) | var(--map-green-shadow-sm) |
| .float-cta:hover | rgba(0,255,136,.35) | var(--map-green-shadow-md) |
| .hero-eyebrow | border-radius: 4px | var(--seed-radius-sm) |
| .hero-term | border-radius: 8px | var(--seed-radius-lg) |
| .term-tag | border-radius: 3px | var(--seed-radius-sm) |
| .term-tag | rgba(0,255,136,.06) | var(--map-green-bg-soft) |
| .section-tag | border-radius: 4px | var(--seed-radius-sm) |
| .diff-block | border-radius: 8px | var(--seed-radius-lg) |
| .framework-table | border-radius: 8px | var(--seed-radius-lg) |
| .fw-label.on | border-radius: 3px | var(--seed-radius-sm) |
| .fw-label.on | rgba(0,255,136,.06) | var(--map-green-bg-soft) |
| .ls-block | border-radius: 8px | var(--seed-radius-lg) |
| .asset-table | border-radius: 8px | var(--seed-radius-lg) |
| .at-id | border-radius: 3px | var(--seed-radius-sm) |
| .git-stats | border-radius: 8px | var(--seed-radius-lg) |
| .data-note | border-radius: 8px | var(--seed-radius-lg) |
| .author-card | border-radius: 8px | var(--seed-radius-lg) |
| .author-log | border-radius: 8px | var(--seed-radius-lg) |
| .pkg-card | border-radius: 8px | var(--card-radius) |
| .pkg-rec | box-shadow: 0 0 48px rgba(0,255,136,.12) | 0 0 48px var(--card-rec-shadow) |
| .pkg-badge | border-radius: 3px | var(--seed-radius-sm) |
| .pkg-btn | border-radius: 6px | var(--btn-radius) |
| .preview-list | border-radius: 8px | var(--seed-radius-lg) |
| .preview-note span | border-radius: 3px | var(--seed-radius-sm) |
| .faq-id | border-radius: 4px | var(--seed-radius-sm) |
| .faq-a-prefix | border-radius: 4px | var(--seed-radius-sm) |
| .final-btn | border-radius: 6px | var(--seed-radius) |
| .final-btn | rgba(0,255,136,.2) | var(--map-green-shadow-sm) |
| .final-btn:hover | rgba(0,255,136,.4) | var(--map-green-shadow-md) |
| .final::before | rgba(0,255,136,.06) | var(--map-glow) |
| .buy-note | border-radius: 8px | var(--seed-radius-lg) |

### B 类 · 保留（一次性值，不应 Token 化）

| 位置 | 值 | 理由 |
|---|---|---|
| .term-dots span:nth-child(1..3) | #ff5f57 / #febc2e / #28c840 | macOS 窗口灯配色，不可变 |
| .tag-st | #fb923c | STATE 标签专用色，语义独立 |
| .hero-term box-shadow | 0 24px 64px rgba(0,0,0,.4) | 一次性阴影 |
| .final::before 第二层 | rgba(139,92,246,.04) | 紫色氛围点缀 |

### C 类 · 间距（14 处，暂不处理）

104px / 120px / 160px / 140px 等非网格值。
理由：layout 节奏值，强行网格化会损失视觉微调空间。

## 行动项

- [x] A 类已在本轮 main.css 中全部 Token 化
- [ ] C 类暂不处理
- [ ] 后续新增 CSS 一律走 Token