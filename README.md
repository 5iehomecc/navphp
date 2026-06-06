# E家导航 (StarNav)

> 一个轻量、美观、高度可定制的个人导航页面

## 项目简介

**E家导航** 是一个基于 PHP + JSON 的轻量级个人导航页面，支持书签分组管理、主题切换、AI 生成背景图、自定义 Favicon 等功能。无需数据库，单文件部署即可使用。

## ✨ 主要特性

### 🎨 界面设计
- **双主题切换**：深色（深青 + 金色）和浅色（薄荷绿）双主题
- **现代玻璃拟态**：半透明背景 + 模糊效果
- **平滑动效**：渐变网格呼吸、卡片悬停光晕、入场动画
- **响应式布局**：完美适配桌面、平板、手机
- **AI 背景图**：集成 Trae API，支持 4 种风格（乡村、海洋、森林、天空）的日间/夜间背景

### 🔖 书签管理
- **分组管理**：自定义分组图标（Emoji）、分组重命名/删除
- **书签增删改查**：支持网址、名称、描述、图标
- **智能 Favicon 抓取**：三级回退（t0.gstatic.cn → ico.kucat.cn → icon.horse）
- **手动图标地址**：优先级最高，可输入网址自动转换或直接粘贴图标链接
- **离线缓存**：抓取的书签信息 localStorage 缓存 7 天

### 🔐 管理员功能
- **密码登录**：SHA-256 + salt 加密存储
- **会话管理**：PHP Session 身份验证
- **CRUD 操作**：完整的分组/书签增删改查

### 🚀 性能优化
- **GZIP 压缩**：智能根据 `Accept-Encoding` 启用
- **DNS-prefetch + preconnect**：加速外部资源
- **字体异步加载**：`media="print"` + `onload`
- **Favicon 懒加载**：`loading="lazy"`
- **HTTP 安全头部**：X-Content-Type-Options、X-Frame-Options、Cache-Control
- **小体积**：nav-page.php 47KB，nav-api.php 3.5KB

## 📦 文件结构

```
.
├── nav-page.php      # 前端主页面（PHP 服务端渲染）
├── nav-api.php       # 后端 API 接口
├── nav-data.json     # 数据存储文件
├── .gitignore        # Git 忽略配置
└── README.md         # 本文档
```

## 🚀 快速开始

### 环境要求
- PHP >= 7.4
- 现代浏览器（Chrome 80+、Edge 80+、Firefox 75+、Safari 13+）

### 部署步骤

1. **克隆仓库**
   ```bash
   git clone https://github.com/5iehomecc/navphp.git
   cd navphp
   ```

2. **启动 PHP 内置服务器**（开发环境）
   ```bash
   php -S 0.0.0.0:8000
   ```

3. **访问页面**
   打开浏览器访问 `http://localhost:8000/nav-page.php`

4. **生产环境部署**
   - 将文件上传到支持 PHP 的服务器
   - 确保 `nav-data.json` 可写（权限 666 或 777）
   - 访问 `nav-page.php` 即可

### Nginx 配置示例

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/navphp;
    index nav-page.php;

    location / {
        try_files $uri $uri/ /nav-page.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

## 🔐 管理员使用

### 首次使用

1. 打开页面，初始状态下分组和书签为空
2. 左侧底部点击"管理员登录"
3. 输入默认密码（参见配置文件）
4. 登录后即可添加分组、添加书签

### 修改密码

默认密码为 `andyr00000`（SHA-256 加密存储在 `nav-data.json` 中）。

修改密码步骤：
1. 编辑 `nav-data.json` 中的 `adminHash` 字段
2. 使用以下 PHP 代码生成新哈希：
   ```php
   <?php
   $p = 'your_new_password';
   echo hash('sha256', $p . '_starnav_salt_2024');
   ?>
   ```
3. 将生成的哈希值替换 `adminHash` 字段

## ⚙️ 配置说明

### 数据格式（nav-data.json）

```json
{
  "adminHash": "44f61792d66021c0030fa37dca5162871345c525f61984b88fa1af16d8117672",
  "siteName": "E家导航",
  "siteDesc": "E家导航 - 最实用的经验，分享最需要的你",
  "groups": [
    {
      "id": "default",
      "name": "默认",
      "emoji": "📌",
      "bookmarks": [
        {
          "id": "bm_xxxxx",
          "url": "https://example.com",
          "name": "示例网站",
          "desc": "网站描述",
          "favicon": "https://t0.gstatic.cn/faviconV2?..."
        }
      ]
    }
  ]
}
```

### 环境变量
无外部依赖，无需配置环境变量。

## 🔄 版本历史

| 版本 | 发布日期 | 主要更新 |
|------|----------|----------|
| v1.6.0 | 2026-06-02 | 代码架构优化、加载速度提升、管理员密码更新 |
| v1.5.0 | 2026-06-01 | 图标优先级优化、Logo 对比度增强 |
| v1.4.0 | 2026-05-31 | 配色全面优化、视觉动效增强 |
| v1.3.0 | 2026-05-30 | 应用 Happy Hues Palette #10 配色 |
| v1.2.0 | 2026-05-29 | 初始版本发布 |

### 版本回滚

```bash
# 查看所有版本标签
git tag -l

# 回滚到指定版本
git checkout v1.5.0    # 或 v1.4.0, v1.3.0, v1.2.0

# 或者创建回滚分支
git checkout -b rollback-v1.5.0 v1.5.0
```

## 🛠️ 技术栈

- **后端**：PHP（无框架，原生 PHP）
- **存储**：JSON 文件（替代传统数据库）
- **前端**：原生 HTML + CSS + JavaScript（无任何前端框架）
- **字体**：Inter + LXGW 霞鹜文楷
- **图标 API**：Google Favicon Service + ico.kucat.cn + icon.horse
- **背景 API**：Trae API (text_to_image)

## 📝 开发指南

### 本地开发

```bash
# 启动开发服务器
php -S localhost:8000

# 调试模式
# 浏览器开发者工具 → Network → 勾选 Disable cache
```

### 调试技巧

1. **查看数据**：`cat nav-data.json | python3 -m json.tool`
2. **清空数据**：删除 `nav-data.json` 文件，下次访问时自动重建
3. **查看 GZIP**：终端 `curl -H "Accept-Encoding: gzip" --compressed http://localhost:8000/nav-page.php`

## 🔒 安全说明

- 密码使用 SHA-256 哈希存储，**不可逆**
- PHP Session 用于身份验证，**不持久化到客户端**
- 所有写操作（增删改）都通过 `nav-api.php` 进行权限校验
- HTTP 安全头部已配置：
  - `X-Content-Type-Options: nosniff` 防止 MIME 嗅探
  - `X-Frame-Options: SAMEORIGIN` 防止点击劫持
  - `Cache-Control: no-store` 防止敏感信息缓存

## 🤝 贡献指南

欢迎提交 Issue 和 Pull Request！

1. Fork 本仓库
2. 创建特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交改动 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 创建 Pull Request

## 📄 许可证

本项目采用 MIT 许可证 - 详见 [LICENSE](LICENSE) 文件

## 🙏 致谢

- 配色灵感：[Happy Hues](https://www.happyhues.co/palettes/10)
- 图标 API：[Google Favicon Service](https://www.google.com/s2/favicons)
- 字体：[LXGW 霞鹜文楷](https://github.com/lxgw/LxgwWenkaiTC)
- AI 背景：[Trae API](https://trae-api-cn.mchost.guru/)

## 📮 联系方式

- 项目地址：[https://github.com/5iehomecc/navphp](https://github.com/5iehomecc/navphp)
- 作者主页：[https://www.5iehome.cc](https://www.5iehome.cc)

---

**E家导航 v1.6.0** - Copyright © 2026 E家分享
