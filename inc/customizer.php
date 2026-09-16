<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Customizer · 首页 12 Section + 评论策略 + 浮动按钮 后台化
 */

/* ═══════════════════════════════════════════════
   PART 1 · 默认值
   ═══════════════════════════════════════════════ */
function ai_defaults() {
    static $d = null;
    if ( $d !== null ) return $d;

    $d = [

        /* ① HERO */
        'hero_eyebrow_text'       => 'ai-native-software-engineering',
        'hero_eyebrow_version'    => 'v2026.09',
        'hero_title'              => '从 <em>代码生成</em><br>到 <em>约束工程</em>',
        'hero_meta_author'        => '李咏燊',
        'hero_meta_pages'         => '20 chapters · 12 appendices',
        'hero_meta_practice'      => '1LinCMS · 13 months',
        'hero_meta_thesis'        => '代码是结果，约束才是生产系统',
        'hero_cta_primary_text'   => 'read --full',
        'hero_cta_primary_url'    => '#pricing',
        'hero_cta_ghost_text'     => 'preview --free',
        'hero_cta_ghost_url'      => '#preview',
        'hero_term_path'          => '~/refund-feature — zsh',

        /* ② PROBLEM */
        'problem_title'           => "AI 写代码越来越快<br>\n但真正的瓶颈<br>\n正在从<em>生成</em>转向<em>控制</em>",
        'problem_desc'            => '代码生成速度提升之后，新的瓶颈出现在返工、Review、架构漂移、上下文失真与经验无法复制',
        'problem_log'             => "T+0m|AI 生成 <span class=\"accent\">23 个文件</span> · 编译通过\nT+3d|测试环境事故 · <span class=\"err\">用户点两次，生成两条退款记录</span>\nT+2w|修，又出错 · 再修，又出错 · <span class=\"err\">返工吞掉所有速度优势</span>\nT+复盘|不是 AI 不会写代码 · <span class=\"accent\">是六个机制同时缺失</span>",
        'problem_symptoms'        => "01|40 分钟生成，返工两周\n02|代码能跑，架构逐渐漂移\n03|同一个概念，每次理解不同\n04|团队用 AI，Review 反而变慢\n05|个人经验，换项目就失效\n06|规则写在文档里，AI 会绕过",

        /* ③ PARADIGM */
        'paradigm_title'          => '过去管理<em>代码</em><br>现在管理<em>约束</em>',
        'paradigm_before_label'   => '传统开发',
        'paradigm_before_flow'    => '需求 → 人 → 代码',
        'paradigm_before_note'    => '人既是设计者也是执行者 · 代码是核心产出',
        'paradigm_after_label'    => 'AI 原生工程',
        'paradigm_after_flow'     => '人 → 约束 → AI → 代码 → 验证 → 演化',
        'paradigm_after_note'     => 'AI 被纳入完整的工程闭环 · 速度可控，质量可验证',
        'paradigm_quote'          => "不要把 AI 当成一个更快的程序员<br>\n把 AI 纳入完整的<em>约束、验证与反馈系统</em>",

        /* ④ FRAMEWORK */
        'framework_title'         => '六个核心维度<br>共同构成 <em>AI-CMM</em>',
        'framework_items'         => "01|Slice|把复杂需求切成 AI 可以稳定执行的工作单元 · 架构切片 / 任务切片 / 上下文切片\n02|Boundary|明确模块、职责与依赖方向 · 规则必须物理化——架构测试、CI 卡口、pre-commit 守卫\n03|Contract|让业务规则和工程规则变成可检查的契约 · Spec / Architecture / Skill / Quality 四类契约\n04|Context|让 AI 在正确的上下文中工作 · 不是资料越多越好，是当前任务真正有效的信息集合\n05|State|任务不能只是一次调用 · 它必须有状态机、幂等键、重试与回滚策略\n06|Evolution|发现 → 拉取 → 适配 → 验证 → 反哺 → 演进 · 一次工作成为下一次工作的起点",
        'framework_divider_on'    => '+2',
        'framework_divider_label' => '贯穿机制',

        /* ⑤ PROJECT */
        'project_title'           => '约束不是概念<br>它落在一组<em>具体文件</em>上',
        'project_files'           => "drwxr-xr-x|docs/|—|CONSTRAINT SURFACE|plain\n-rw-r--r--|ARCHITECTURE.md|2.1k|BOUNDARY|tag-bd\n-rw-r--r--|AGENTS.md|1.4k|CONTEXT|tag-ctx\n-rw-r--r--|CONTEXT.md|0.9k|CONTEXT|tag-ctx\ndrwxr-xr-x|.agents/skills/|—|SLICE|tag-sl\n-rw-r--r--|SKILL.md|3.2k|SLICE|tag-sl\ndrwxr-xr-x|contracts/|—|CONTRACT|tag-ct\ndrwxr-xr-x|tasks/current/|—|STATE|tag-st\n-rw-r--r--|plan.json|0.6k|STATE|tag-st\ndrwxr-xr-x|tests/architecture/|—|BOUNDARY|tag-bd\ndrwxr-xr-x|scripts/|—|GATE|tag-bd\n-rwxr-xr-x|check-dependency.sh|0.4k|BOUNDARY|tag-bd\n-rwxr-xr-x|ci.yml|0.8k|GATE|tag-bd",

        /* ⑥ ASSETS */
        'assets_title'            => '不是一堆 PDF<br>是可以<em>进入项目</em>的资产',
        'assets_groups'           => "[foundations · A–D]\nA|ARCHITECTURE.md|架构规范 + 依赖矩阵 + 架构测试\nB|AGENTS.md|AI 行为准则 + 路由中枢\nC|SKILL.md|技能定义 + 反模式拦截 + 模板目录\nD|.tpl 全家桶|10 个 PHP 代码模板\n[engineering · E–H]\nE|架构测试模板|PHPArkitect + 自定义脚本 + CI 集成\nF|MCP 配置模板|CodeGraph / Filesystem / Git / Database\nG|四会话 Prompt|完整实录 + 四会话 Prompt 模板\nH|合规自查报告|分层 / 命名 / 边界 / 测试\n[governance · I–L]\nI|可继承资产总清单|五类资产 + 继承 + 演化记录\nJ|角色-能力矩阵|11 个角色的五维配置\nK|AI-CMM 术语表|核心术语 + 数字分级说明\nL|参考文献与谱系|方法论谱系 + 引用规范",

        /* ⑦ DATA */
        'data_title'              => '1LinCMS<br>实践样本',
        'data_desc'               => '理论不是终点 · 方法论落到真实工程结构中，形成了可观察、可验证的数据',
        'data_stats'              => "7|applications\n38|plugins\n13|months\n68|first-pass rate %",
        'data_note'               => '以上为 1LinCMS 项目内部观察数据 · 数据可以支持实践观察，但不能单独构成严格因果证明 · 具体边界条件见第 19 章',
        'data_note_tag'           => 'NOTE',

        /* ⑧ AUTHOR */
        'author_title'            => '作者',
        'author_name'             => '李咏燊',
        'author_role'             => 'AI 原生软件工程实践者',
        'author_avatar_char'      => '李',
        'author_timeline'         => "2025.08|1LinCMS 项目启动|\n2025.11|中央治理尝试与失败|\n2026.02|重构为「一控四载体」生态结构|\n2026.04|重度使用 AI Coding · 退款功能事故|\n2026.05|形成 AI-CMM 与 Core 4+2 理论框架|\n2026.09|本书封版|accent",
        'author_quote'            => '2026 年 4 月，我开始重度使用 AI Coding 做开发。<strong>从一个真实的退款功能事故出发</strong>，经历了 AI 失控、中央治理崩溃、演进式复用成型，最终把实践抽象成一套方法论。这本书是从 1LinCMS 十三个月真实工程周期里长出来的。',

        /* ⑨ PRICING */
        'pricing_title'           => '三种版本<br>三种使用深度',
        'pricing_packages'        => "[reading|¥59.8|适合：系统理解方法论|0]\nchapters|20 章完整正文|ok\ntoc|正式目录|ok\nappendix|—|no\nupdates|—|no\nsupport|—|no\n[practice|¥99.8|适合：真正落地项目|1]\nchapters|20 章完整正文|ok\nappendix|A–L 工程资产包|ok\ntemplates|ARCHITECTURE / AGENTS / SKILL|ok\nprompts|四会话 Prompt 模板|ok\nmatrix|11 个角色配置|ok\n[professional|¥298|适合：团队与长期实践|0]\nchapters|全部正文|ok\nappendix|A–L 工程资产包|ok\nupdates|1 年更新权|ok\nsupport|专属答疑|ok\nextra|1LinCMS 精选实践文档|ok",
        'pricing_buy_text'        => '请联系作者获取支付方式 · 或访问对应平台商品页',
        'pricing_buy_link_text'   => '查看常见问题 →',
        'pricing_buy_link_url'    => '#faq',

        /* ⑩ PREVIEW */
        'preview_title'           => '先从问题本身<em>开始</em>',
        'preview_desc'            => '免费开放 · 序言 + 第 1 章 + 第 2 章 + 第 3 章 · 共约 26,700 字',
        'preview_cta_text'        => 'open ./preview/',
        'preview_cta_url'         => '/preview/',
        'preview_note_1'          => '无需注册',
        'preview_note_2'          => '无需付费',
        'preview_note_3'          => '直接阅读',

        /* ⑪ FAQ */
        'faq_title'               => 'FAQ',
        'faq_desc'                => '常见问题的终端风格解答 · 点击任意行展开',
        'faq_items'               => "[Q01|~40 字]\n这是一本什么类型的产品？\n方法论内容 + 工程资产数字产品 · 不是 Prompt 集合，不是工具评测，不是 IDE 操作教程\n\n[Q02|~120 字]\n实践版和阅读版最大的区别？\n阅读版是完整正文（20 章）· 实践版在阅读版基础上增加附录 A–L 工程资产包——ARCHITECTURE.md、AGENTS.md、SKILL.md 模板、四会话 Prompt、角色-能力矩阵等，面向准备直接落地项目的读者\n\n[Q03|~80 字]\n工程资产可以直接放进项目吗？\n设计目标就是可复制和继续使用 · 具体项目仍需根据技术栈、架构和组织规则进行适配 · 附录每一项都标注了来源、版本、适用场景\n\n[Q04|4 条]\n适合谁？\n- 使用 Claude Code / Cursor / Codex 开发复杂系统的开发者\n- 建立团队统一 AI Coding 规范的技术负责人\n- 关注 AI 原生系统中边界、契约、上下文与持续演化的架构师\n- 希望把个人经验沉淀为团队能力的团队负责人\n\n[Q05|~90 字]\n不适合谁？\n如果你只是在找一个更好的 Prompt，或者只是想比较哪个工具更好用，这套产品可能不是当前最需要的 · 它更适合希望解决「如何让 AI 长期、稳定、可控地参与复杂软件生产」这个问题的人\n\n[Q06|~40 字]\n免费试读包含哪些内容？\n序言 + 第 1 章 + 第 2 章 + 第 3 章 · 共约 26,700 字 · [前往试读](#preview)",

        /* ⑫ FINAL */
        'final_exit_cmd'          => 'exit',
        'final_outputs'           => "session ended after 20 chapters\nassets inherited: 12 appendices\nnext intent: your project",
        'final_title'             => '代码是结果，<br><em>约束才是生产系统</em>',
        'final_cta_primary_text'  => 'start --now',
        'final_cta_primary_url'   => '#pricing',
        'final_cta_ghost_text'    => 'preview --free',
        'final_cta_ghost_url'     => '#preview',
        'final_status_1'          => 'session ended',
        'final_status_2'          => 'thanks for reading',

        /* ⑬ 浮动操作按钮（6 段：类型|图标|标签|URL|target|scope） */
        'float_actions'           => "link|key|unlock --all --assets|#pricing|_self|home\nshare|share-2|分享本页|||all\ntop|arrow-up|回到顶部|||all",

        /* ⑭ 评论策略 */
        'comment_login_required'   => '1',
        'comment_guest_allow_ids'  => '',
        'comment_guest_allow_cats' => '',
        'comment_guest_allow_tags' => '',
    ];

    return $d;
}

function ai_default( $key ) {
    $d = ai_defaults();
    return $d[ $key ] ?? '';
}


/* ═══════════════════════════════════════════════
   PART 2 · 前端读取
   ═══════════════════════════════════════════════ */
function ai_opt( $key, $fallback = null ) {
    $default = $fallback !== null ? $fallback : ai_default( $key );
    return get_theme_mod( 'ai_' . $key, $default );
}

function ai_opt_lines( $key ) {
    $raw = (string) ai_opt( $key );
    $out = [];
    foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
        $line = trim( $line );
        if ( $line === '' ) continue;
        $out[] = array_map( 'trim', explode( '|', $line ) );
    }
    return $out;
}

function ai_opt_groups( $key ) {
    $raw = (string) ai_opt( $key );
    $groups = [];
    $cur = null;
    foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
        $line = trim( $line );
        if ( $line === '' ) continue;
        if ( preg_match( '/^\[(.+?)\]\s*(.*)$/', $line, $m ) ) {
            if ( $cur !== null ) $groups[] = $cur;
            $cur = [ 'head' => array_map( 'trim', explode( '|', $m[1] ) ), 'items' => [] ];
        } elseif ( $cur !== null ) {
            $cur['items'][] = array_map( 'trim', explode( '|', $line ) );
        }
    }
    if ( $cur !== null ) $groups[] = $cur;
    return $groups;
}

function ai_opt_blocks( $key ) {
    $raw  = trim( (string) ai_opt( $key ) );
    $out  = [];
    $blocks = preg_split( '/\n\s*\n/', $raw );
    foreach ( $blocks as $block ) {
        $lines = array_map( 'trim', preg_split( '/\r\n|\r|\n/', $block ) );
        if ( empty( $lines ) ) continue;
        $head_line = array_shift( $lines );
        if ( ! preg_match( '/^\[(.+?)\]$/', $head_line, $m ) ) continue;
        $q = array_shift( $lines );
        $a = implode( "\n", $lines );
        $out[] = [
            'head' => array_map( 'trim', explode( '|', $m[1] ) ),
            'q'    => $q,
            'a'    => $a,
        ];
    }
    return $out;
}


/* ═══════════════════════════════════════════════
   PART 3 · Schema
   ═══════════════════════════════════════════════ */
function ai_schema() {
    return [

        'hero' => [
            'title'    => '① Hero 首屏',
            'priority' => 10,
            'fields'   => [
                'hero_eyebrow_text'     => [ 'label' => 'Eyebrow 文字' ],
                'hero_eyebrow_version'  => [ 'label' => 'Eyebrow 版本号' ],
                'hero_title'            => [ 'label' => '主标题', 'type' => 'textarea', 'desc' => '支持 <em> 和 <br>' ],
                'hero_meta_author'      => [ 'label' => 'meta · author' ],
                'hero_meta_pages'       => [ 'label' => 'meta · pages' ],
                'hero_meta_practice'    => [ 'label' => 'meta · practice' ],
                'hero_meta_thesis'      => [ 'label' => 'meta · thesis' ],
                'hero_cta_primary_text' => [ 'label' => '主按钮文字' ],
                'hero_cta_primary_url'  => [ 'label' => '主按钮 URL', 'type' => 'url' ],
                'hero_cta_ghost_text'   => [ 'label' => '次按钮文字' ],
                'hero_cta_ghost_url'    => [ 'label' => '次按钮 URL', 'type' => 'url' ],
                'hero_term_path'        => [ 'label' => '终端标题路径' ],
            ],
        ],

        'problem' => [
            'title'    => '② Problem 问题',
            'priority' => 20,
            'fields'   => [
                'problem_title'    => [ 'label' => '标题', 'type' => 'textarea' ],
                'problem_desc'     => [ 'label' => '描述', 'type' => 'textarea' ],
                'problem_log'      => [ 'label' => '日志', 'type' => 'textarea', 'desc' => '每行：时间|内容' ],
                'problem_symptoms' => [ 'label' => '症状列表', 'type' => 'textarea', 'desc' => '每行：编号|文本' ],
            ],
        ],

        'paradigm' => [
            'title'    => '③ Paradigm 范式',
            'priority' => 30,
            'fields'   => [
                'paradigm_title'         => [ 'label' => '标题', 'type' => 'textarea' ],
                'paradigm_before_label'  => [ 'label' => 'Before · 标签' ],
                'paradigm_before_flow'   => [ 'label' => 'Before · 流程' ],
                'paradigm_before_note'   => [ 'label' => 'Before · 说明', 'type' => 'textarea' ],
                'paradigm_after_label'   => [ 'label' => 'After · 标签' ],
                'paradigm_after_flow'    => [ 'label' => 'After · 流程' ],
                'paradigm_after_note'    => [ 'label' => 'After · 说明', 'type' => 'textarea' ],
                'paradigm_quote'         => [ 'label' => '引用句', 'type' => 'textarea' ],
            ],
        ],

        'framework' => [
            'title'    => '④ Framework 框架',
            'priority' => 40,
            'fields'   => [
                'framework_title'         => [ 'label' => '标题', 'type' => 'textarea' ],
                'framework_items'         => [ 'label' => '六个维度', 'type' => 'textarea', 'desc' => '每行：ID|NAME|DESC' ],
                'framework_divider_on'    => [ 'label' => '分隔条 · 左' ],
                'framework_divider_label' => [ 'label' => '分隔条 · 右' ],
            ],
        ],

        'project' => [
            'title'    => '⑤ Project 工程结构',
            'priority' => 50,
            'fields'   => [
                'project_title' => [ 'label' => '标题', 'type' => 'textarea' ],
                'project_files' => [ 'label' => '文件列表', 'type' => 'textarea', 'desc' => '每行：MODE|NAME|SIZE|TAG|TAG_CLASS' ],
            ],
        ],

        'assets' => [
            'title'    => '⑥ Assets 资产',
            'priority' => 60,
            'fields'   => [
                'assets_title'  => [ 'label' => '标题', 'type' => 'textarea' ],
                'assets_groups' => [ 'label' => '资产清单', 'type' => 'textarea', 'desc' => '[组名] 单独一行开始新组；条目：ID|NAME|DESC' ],
            ],
        ],

        'data' => [
            'title'    => '⑦ Data 数据',
            'priority' => 70,
            'fields'   => [
                'data_title'    => [ 'label' => '标题', 'type' => 'textarea' ],
                'data_desc'     => [ 'label' => '描述', 'type' => 'textarea' ],
                'data_stats'    => [ 'label' => '数字', 'type' => 'textarea', 'desc' => '每行：数字|标签' ],
                'data_note_tag' => [ 'label' => 'Note 标签' ],
                'data_note'     => [ 'label' => 'Note 内容', 'type' => 'textarea' ],
            ],
        ],

        'author' => [
            'title'    => '⑧ Author 作者',
            'priority' => 80,
            'fields'   => [
                'author_title'       => [ 'label' => '标题' ],
                'author_name'        => [ 'label' => '姓名' ],
                'author_role'        => [ 'label' => '角色' ],
                'author_avatar_char' => [ 'label' => '头像字符' ],
                'author_timeline'    => [ 'label' => '时间线', 'type' => 'textarea', 'desc' => '每行：时间|内容|accent' ],
                'author_quote'       => [ 'label' => '引用段', 'type' => 'textarea' ],
            ],
        ],

        'pricing' => [
            'title'    => '⑨ Pricing 版本',
            'priority' => 90,
            'fields'   => [
                'pricing_title'         => [ 'label' => '标题', 'type' => 'textarea' ],
                'pricing_packages'      => [ 'label' => '三个版本', 'type' => 'textarea', 'desc' => '见 ai_defaults() 注释' ],
                'pricing_buy_text'      => [ 'label' => '购买提示' ],
                'pricing_buy_link_text' => [ 'label' => '链接文字' ],
                'pricing_buy_link_url'  => [ 'label' => '链接 URL', 'type' => 'url' ],
            ],
        ],

        'preview' => [
            'title'    => '⑩ Preview 试读',
            'priority' => 100,
            'fields'   => [
                'preview_title'    => [ 'label' => '标题', 'type' => 'textarea' ],
                'preview_desc'     => [ 'label' => '描述', 'type' => 'textarea' ],
                'preview_cta_text' => [ 'label' => 'CTA 文字' ],
                'preview_cta_url'  => [ 'label' => 'CTA URL', 'type' => 'url' ],
                'preview_note_1'   => [ 'label' => 'Note 1' ],
                'preview_note_2'   => [ 'label' => 'Note 2' ],
                'preview_note_3'   => [ 'label' => 'Note 3' ],
            ],
        ],

        'faq' => [
            'title'    => '⑪ FAQ 常见问题',
            'priority' => 110,
            'fields'   => [
                'faq_title' => [ 'label' => '标题' ],
                'faq_desc'  => [ 'label' => '描述' ],
                'faq_items' => [ 'label' => '问答列表', 'type' => 'textarea', 'desc' => '空行分隔块；[ID|META] / Q / A' ],
            ],
        ],

        'final' => [
            'title'    => '⑫ Final 结尾',
            'priority' => 120,
            'fields'   => [
                'final_exit_cmd'         => [ 'label' => 'exit 命令' ],
                'final_outputs'          => [ 'label' => '输出行', 'type' => 'textarea' ],
                'final_title'            => [ 'label' => '大标题', 'type' => 'textarea' ],
                'final_cta_primary_text' => [ 'label' => '主按钮文字' ],
                'final_cta_primary_url'  => [ 'label' => '主按钮 URL', 'type' => 'url' ],
                'final_cta_ghost_text'   => [ 'label' => '次按钮文字' ],
                'final_cta_ghost_url'    => [ 'label' => '次按钮 URL', 'type' => 'url' ],
                'final_status_1'         => [ 'label' => '状态行 1' ],
                'final_status_2'         => [ 'label' => '状态行 2' ],
            ],
        ],

        'float' => [
            'title'    => '⑬ 浮动操作按钮',
            'priority' => 130,
            'fields'   => [
                'float_actions' => [
                    'label' => '按钮列表',
                    'type'  => 'textarea',
                    'desc'  => '每行 6 段：类型|图标|标签|URL|target|scope' . "\n"
                             . '' . "\n"
                             . '【类型】' . "\n"
                             . '  link   普通链接（需填 URL）' . "\n"
                             . '  top    平滑滚动到顶部' . "\n"
                             . '  share  系统分享 / 降级复制链接' . "\n"
                             . '' . "\n"
                             . '【scope · 显示范围】' . "\n"
                             . '  留空 / all   所有页面' . "\n"
                             . '  home         仅首页' . "\n"
                             . '  singular     仅页面 / 文章' . "\n"
                             . '  archive      仅列表页' . "\n"
                             . '' . "\n"
                             . '【图标】' . "\n"
                             . '  arrow-up / arrow-down / share-2 / key / book / message' . "\n"
                             . '  mail / download / external-link / link / user / help-circle' . "\n"
                             . '  file-text / layers / package / terminal' . "\n"
                             . '' . "\n"
                             . '【默认值】' . "\n"
                             . '  link|key|unlock --all --assets|#pricing|_self|home' . "\n"
                             . '  share|share-2|分享本页|||all' . "\n"
                             . '  top|arrow-up|回到顶部|||all',
                ],
            ],
        ],

        'comment_policy' => [
            'title'    => '⑭ 评论策略',
            'priority' => 140,
            'fields'   => [
                'comment_login_required' => [
                    'label' => '仅登录用户可评论',
                    'type'  => 'checkbox',
                    'desc'  => '开启后，未登录访客只能阅读评论，不能发表',
                ],
                'comment_guest_allow_ids' => [
                    'label' => '例外 · 页面 / 文章 ID',
                    'type'  => 'textarea',
                    'desc'  => '允许游客评论的文章或页面 ID，多个用逗号 / 空格 / 换行分隔',
                ],
                'comment_guest_allow_cats' => [
                    'label' => '例外 · 分类 slug',
                    'type'  => 'textarea',
                    'desc'  => '这些分类下的文章允许游客评论，多个用逗号 / 空格 / 换行分隔',
                ],
                'comment_guest_allow_tags' => [
                    'label' => '例外 · 标签 slug',
                    'type'  => 'textarea',
                    'desc'  => '这些标签下的文章允许游客评论，多个用逗号 / 空格 / 换行分隔',
                ],
            ],
        ],

    ];
}


/* ═══════════════════════════════════════════════
   PART 4 · 注册入口
   ═══════════════════════════════════════════════ */
add_action( 'customize_register', 'ai_customize_register' );

function ai_customize_register( $wp_customize ) {

    $wp_customize->add_panel( 'ai_coding', [
        'title'       => 'AI Coding 主题',
        'description' => '首页 12 Section + 评论策略 + 浮动按钮',
        'priority'    => 10,
    ] );

    foreach ( ai_schema() as $sec_id => $sec ) {
        $wp_customize->add_section( 'ai_' . $sec_id, [
            'title'    => $sec['title'],
            'panel'    => 'ai_coding',
            'priority' => $sec['priority'] ?? 10,
        ] );

        foreach ( $sec['fields'] as $key => $field ) {
            ai_register_field( $wp_customize, 'ai_' . $sec_id, $key, $field );
        }
    }
}

function ai_register_field( $wp_customize, $section, $key, $field ) {
    $id   = 'ai_' . $key;
    $type = $field['type'] ?? 'text';

    $sanitize = $field['sanitize'] ?? (
        $type === 'url'        ? 'esc_url_raw' :
        ( $type === 'textarea' ? 'wp_kses_post' :
        ( $type === 'checkbox' ? 'ai_sanitize_checkbox' : 'sanitize_text_field' ) )
    );

    $wp_customize->add_setting( $id, [
        'default'           => ai_default( $key ),
        'sanitize_callback' => $sanitize,
        'transport'         => 'refresh',
    ] );

    $wp_customize->add_control( $id, [
        'label'       => $field['label'],
        'description' => $field['desc'] ?? '',
        'section'     => $section,
        'type'        => $type,
    ] );
}

function ai_sanitize_checkbox( $value ) {
    return ( $value === '1' || $value === 1 || $value === true ) ? '1' : '';
}