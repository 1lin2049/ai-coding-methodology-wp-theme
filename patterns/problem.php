<?php
/**
 * Title: Problem 问题
 * Slug: ai-coding/problem
 * Categories: ai-coding/sections
 * Description: 日志时间线 + 症状网格
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!-- wp:html -->
<section class="section" id="problem">
  <div class="wrap">
    <div class="section-head">
      <div class="section-tag"><span class="prompt">$</span><span>cat</span><span class="path">./01-problem.md</span></div>
      <h2 class="section-title">
        AI 写代码越来越快<br>
        但真正的瓶颈<br>
        正在从<em>生成</em>转向<em>控制</em>
      </h2>
      <p class="section-desc">代码生成速度提升之后，新的瓶颈出现在返工、Review、架构漂移、上下文失真与经验无法复制</p>
    </div>

    <div class="log">
      <div class="log-line">
        <span class="log-time">T+0m</span>
        <span class="log-msg">AI 生成 <span class="accent">23 个文件</span> · 编译通过</span>
      </div>
      <div class="log-line">
        <span class="log-time">T+3d</span>
        <span class="log-msg">测试环境事故 · <span class="err">用户点两次，生成两条退款记录</span></span>
      </div>
      <div class="log-line">
        <span class="log-time">T+2w</span>
        <span class="log-msg">修，又出错 · 再修，又出错 · <span class="err">返工吞掉所有速度优势</span></span>
      </div>
      <div class="log-line">
        <span class="log-time">T+复盘</span>
        <span class="log-msg">不是 AI 不会写代码 · <span class="accent">是六个机制同时缺失</span></span>
      </div>
    </div>

    <div class="divider-label"><span>// common symptoms</span></div>

    <div class="symptom-grid">
      <div class="symptom"><span class="snum">01</span><span class="stext">40 分钟生成，返工两周</span></div>
      <div class="symptom"><span class="snum">02</span><span class="stext">代码能跑，架构逐渐漂移</span></div>
      <div class="symptom"><span class="snum">03</span><span class="stext">同一个概念，每次理解不同</span></div>
      <div class="symptom"><span class="snum">04</span><span class="stext">团队用 AI，Review 反而变慢</span></div>
      <div class="symptom"><span class="snum">05</span><span class="stext">个人经验，换项目就失效</span></div>
      <div class="symptom"><span class="snum">06</span><span class="stext">规则写在文档里，AI 会绕过</span></div>
    </div>
  </div>
</section>
<!-- /wp:html -->