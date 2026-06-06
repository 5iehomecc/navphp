# StarNav v1.7.0 布局重构计划（已完成）

## 需求概述
参考 https://startab.cn/123 进行布局大调整，主要包括：
1. 分组拖拽移动（改变分组顺序）
2. 书签信息简洁化：图标+名称一行，描述单独一行（可选，浅色字体）
3. 数据导出功能：支持选择格式（Bookmarks HTML / JSON）

## 当前状态
**该计划已于 2026-06-02 完成实施！** 所有功能已实现并运行在 `http://localhost:8111/nav-page.php`

## 已实现的改动

### 1. 书签卡片样式重构
**文件**: `/workspace/nav-page.php`

新布局结构：
```
┌─────────────────────────────┐
│ [图标] 网站名称             │  ← 第一行：flex row, gap 8px
│         网站描述（浅色）     │  ← 第二行：单独一行，margin-left: 32px
└─────────────────────────────┘
```

CSS 关键改动：
- `.bookmark-card`: `flex-direction: column`, `align-items: flex-start`, `gap: 4px`, `height: auto`
- `.bm-row1`: `display: flex`, `align-items: center`, `gap: 8px`, `width: 100%`
- `.bookmark-desc`: `margin-left: 32px`, `color: var(--text-muted)`, `font-size: 0.7rem`
- 描述行通过 `<?php if(!empty($bm['desc'])):?>` 条件渲染，无描述时不显示

### 2. 分组拖拽功能
**文件**: `/workspace/nav-page.php`

实现方式：原生 HTML5 Drag and Drop API
- `body.admin-mode .group`: `cursor: grab`, `user-select: none`
- `.group.dragging`: `opacity: 0.5`, `border-color: var(--accent)`
- `.group.drag-over`: `border-color: var(--accent)`, `box-shadow: 0 0 0 2px var(--accent-glow)`

拖拽逻辑：
- `initDrag()`: 为每个 `.group` 绑定 dragstart/dragover/dragenter/dragleave/drop/dragend 事件
- `onDrop()`: 交换 DOM 位置后调用 `sGOrder()` 保存到服务器
- `sGOrder()`: 收集分组顺序，POST 到 `nav-api.php?action=reorderGroups`

### 3. 数据导出功能
**文件**: `/workspace/nav-page.php` + `/workspace/nav-api.php`

前端模态框：
- `id="exportM"` 模态框，包含两个单选按钮（html/json）
- "开始导出"按钮调用 `doExport()` 函数
- 导出选项卡片：`.export-option`，悬停高亮，选中时 `.selected`

API 端点：
- `exportJSON`: 返回 `nav-data.json` 完整数据，Content-Disposition 触发下载
- `exportHTML`: 生成 Netscape Bookmark 格式 HTML，兼容 Chrome/Firefox/Edge 导入
- 导出按钮：管理员侧边栏新增"数据导出"按钮，调用 `oEM()` 打开模态框

## 修改文件清单

| 文件 | 修改内容 | 行数变化 |
|------|---------|---------|
| `nav-page.php` | 书签卡片 CSS 重构 + 拖拽 JS + 导出模态框 + 导出 JS | +200 行 |
| `nav-api.php` | 新增 reorderGroups、exportJSON、exportHTML 路由 | +40 行 |
| `README.md` | 更新 v1.7.0 功能说明 | 更新 |

## 验证步骤（已完成）
1. ✅ PHP 语法检查通过（nav-page.php, nav-api.php）
2. ✅ 书签卡片新布局：图标+名称一行，描述单独一行
3. ✅ 分组拖拽功能：管理员模式下可拖拽排序
4. ✅ 数据导出：JSON 和 HTML 格式均可正常下载
5. ✅ 服务器运行正常：`http://localhost:8111/nav-page.php`

## 管理员信息
- 密码：`andyr00000`
- 拖拽排序：登录后直接拖拽分组卡片
- 数据导出：侧边栏点击"数据导出"按钮
