<?php
/**
 * Title: 03 · Framework 六维框架
 * Slug: ai-coding/framework
 * Categories: ai-coding/sections
 * Description: AI-CMM 六个核心维度清单
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- wp:html -->
<section class="section" id="framework">
  <div class="wrap">
    <div class="section-head reveal">
      <div class="section-tag"><span class="prompt">$</span><span>tree</span><span class="path">framework/ -L 1</span></div>
      <h2 class="section-title">六个核心维度<br>共同构成 <em>AI-CMM</em></h2>
    </div>
    <div class="framework-table reveal">
      <div class="ft-head">
        <span class="ft-h">ID</span>
        <span class="ft-h">NAME</span>
        <span class="ft-h">DESC</span>
      </div>
      <div class="ft-row">
        <span class="ft-id">01</span>
        <span class="ft-name">Slice</span>
        <span class="ft-desc">把复杂需求切成 AI 可以稳定执行的工作单元 · 架构切片 / 任务切片 / 上下文切片</span>
      </div>
      <div class="ft-row">
        <span class="ft-id">02</span>
        <span class="ft-name">Boundary</span>
        <span class="ft-desc">明确模块、职责与依赖方向 · 规则必须物理化——架构测试、CI 卡口、pre-commit 守卫</span>
      </div>
      <div class="ft-row">
        <span class="ft-id">03</span>
        <span class="ft-name">Contract</span>
        <span class="ft-desc">让业务规则和工程规则变成可检查的契约 · Spec / Architecture / Skill / Quality 四类契约</span>
      </div>
      <div class="ft-row">
        <span class="ft-id">04</span>
        <span class="ft-name">Context</span>
        <span class="ft-desc">让 AI 在正确的上下文中工作 · 不是资料越多越好，是当前任务真正有效的信息集合</span>
      </div>
      <div class="ft-row">
        <span class="ft-id">05</span>
        <span class="ft-name">State</span>
        <span class="ft-desc">任务不能只是一次调用 · 它必须有状态机、幂等键、重试与回滚策略</span>
      </div>
      <div class="ft-row">
        <span class="ft-id">06</span>
        <span class="ft-name">Evolution</span>
        <span class="ft-desc">发现 → 拉取 → 适配 → 验证 → 反哺 → 演进 · 一次工作成为下一次工作的起点</span>
      </div>
      <div class="framework-divider">
        <span class="fw-label on">+2</span>
        <span class="fw-hr"></span>
        <span class="fw-label">贯穿机制</span>
      </div>
    </div>
  </div>
</section>
<!-- /wp:html -->