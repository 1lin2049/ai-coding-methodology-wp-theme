<?php
/**
 * Title: Hero
 * Slug: ai-coding/hero
 * Categories: ai-coding/sections
 * Description: 首页 Hero 区 · 主标题 + 元信息 + 终端
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- wp:group {"className":"hero","layout":{"type":"constrained"}} -->
<section class="hero" id="hero">
  <div class="wrap hero-wrap">

    <div class="hero-eyebrow">
      <span class="dot"></span>
      <span>ai-native-software-engineering</span>
      <span class="sep">//</span>
      <span>v2026.09</span>
    </div>

    <div class="hero-cols">
      <div class="hero-left">
        <h1 class="hero-title">
          从 <em>代码生成</em><br>到 <em>约束工程</em><span class="hero-cursor"></span>
        </h1>

        <div class="hero-meta">
          <div class="hm-item"><span class="hm-key">author</span><span class="hm-val">李咏燊</span></div>
          <div class="hm-item"><span class="hm-key">pages</span><span class="hm-val">20 chapters · 12 appendices</span></div>
          <div class="hm-item"><span class="hm-key">practice</span><span class="hm-val">1LinCMS · 13 months</span></div>
          <div class="hm-item hm-item-wide">
            <span class="hm-key">thesis</span>
            <span class="hm-val accent">代码是结果，约束才是生产系统</span>
          </div>
        </div>

        <div class="hero-actions">
          <a class="btn btn-primary" href="#pricing">
            <span class="prompt">$</span><span>read --full</span><span class="btn-arrow">→</span>
          </a>
          <a class="btn btn-ghost" href="#preview">
            <span class="prompt">$</span><span>preview --free</span>
          </a>
        </div>
      </div>

      <div class="hero-term">
        <div class="term-head">
          <div class="term-dots"><span></span><span></span><span></span></div>
          <div class="term-path">~/refund-feature — zsh</div>
          <div class="term-tag">LIVE</div>
        </div>
        <div class="term-body">
          <div class="line"><span class="p">$</span><span class="cmd">ai generate</span> <span class="arg">"实现订单退款功能"</span></div>
          <div class="line out"><span class="arrow">→</span>生成 23 个文件 · 耗时 40 分钟</div>
          <div class="line out"><span class="arrow">→</span>compile <span class="ok">✓</span> · unit test <span class="ok">✓</span></div>
          <div class="line gap"></div>
          <div class="line"><span class="p">$</span><span class="cmd">deploy</span> <span class="arg">staging</span></div>
          <div class="line diff-m"><span class="arrow">-</span>T+3d  <span class="err">4 个线上事故</span></div>
          <div class="line diff-m" data-mobile-hide><span class="arrow">-</span>重复退款 / 状态混乱 / 跨应用崩溃 / 通道异常</div>
          <div class="line gap"></div>
          <div class="line comment"><span class="arrow">#</span>复盘：不是 AI 不会写代码</div>
          <div class="line comment"><span class="arrow">#</span>是 6 个机制同时缺失</div>
          <div class="line gap"></div>
          <div class="line"><span class="p">$</span><span class="cmd">cat</span> <span class="arg">fix.log</span></div>
          <div class="line diff-p"><span class="arrow">+</span>Slice     切片粒度</div>
          <div class="line diff-p"><span class="arrow">+</span>Boundary  边界约束</div>
          <div class="line diff-p"><span class="arrow">+</span>Contract  契约定义</div>
          <div class="line diff-p"><span class="arrow">+</span>Context   上下文管理</div>
          <div class="line diff-p"><span class="arrow">+</span>State     状态机</div>
          <div class="line diff-p"><span class="arrow">+</span>Evolution 演进复用</div>
        </div>
      </div>
    </div>

    <div class="hero-foot">
      <span>SLICE</span><span class="dot-sep">·</span>
      <span>BOUNDARY</span><span class="dot-sep">·</span>
      <span>CONTRACT</span><span class="dot-sep">·</span>
      <span>CONTEXT</span><span class="dot-sep">·</span>
      <span>STATE</span><span class="dot-sep">·</span>
      <span>EVOLUTION</span>
    </div>

  </div>
</section>
<!-- /wp:group -->