# X-Files - FBI Paranormal Cases Division

A themed web application built with PHP 8.2+ and Bootstrap 5, featuring a full blog CMS with multilingual support (English, French, Italian).

Itamde - https://itamde.com

## Features

- **Blog CMS (WordPress-like)** - Article editor with TinyMCE, categories with parent hierarchy, dynamic tags, featured images, SEO fields, draft/schedule/publish workflow
- **Multilingual** - Full i18n support for UI (EN, FR, IT) + per-article language with translation linking
- **SEO** - SEO title, meta description, Open Graph fields, Google preview, clean slugs
- **Role-based access** - Agent (read-only), Editor (write/publish), Admin (full access)
- **Authentication** - Login, registration with secret code, bcrypt hashing, session management
- **Dashboard** - Case management with stats and activity table
- **Classified area** - Secret password-protected zone
- **X-Files theme** - Dark UI with green glow effects, responsive Bootstrap 5

## Requirements

- PHP 8.2+ (with `intl` extension for slug transliteration)
- MySQL 8.0+ / MariaDB 10.5+
- Apache (XAMPP recommended)

## Installation

1. Clone this repository:
   ```bash
   git clone <repo-url> /path/to/htdocs/xfilesexercice
   ```

2. Import the database:
   ```bash
   mysql -u root < database.sql
   ```

3. Configure `config/database.php` if needed.

4. Ensure `uploads/` directory is writable:
   ```bash
   chmod 755 uploads/
   ```

5. Open: `http://localhost/xfilesexercice/`

## Default Credentials

| User    | Username  | Password   | Role   |
|---------|-----------|------------|--------|
| Mulder  | mulder    | password   | Editor |
| Scully  | scully    | password   | Editor |
| Skinner | skinner   | password   | Admin  |

**Secret code:** `thetruthisoutthere`

## Project Structure

```
xfilesexercice/
├── admin/
│   ├── index.php               # Admin dashboard
│   ├── articles.php            # Article list + filters
│   ├── article-edit.php        # Article editor (TinyMCE + SEO)
│   ├── categories.php          # Category management (hierarchical)
│   ├── tags.php                # Tag management
│   └── _sidebar.php            # Admin sidebar nav
├── assets/
│   ├── css/style.css           # X-Files themed CSS
│   └── img/                    # Theme images (11 images)
├── config/
│   ├── app.php                 # App settings + secret code
│   └── database.php            # PDO database connection
├── includes/
│   ├── header.php              # HTML header + navbar
│   ├── footer.php              # HTML footer
│   └── functions.php           # Helpers (i18n, auth, slugify, categories, etc.)
├── lang/
│   ├── en.php                  # English (100+ keys)
│   ├── fr.php                  # French
│   └── it.php                  # Italian
├── process/
│   ├── login.php               # Login handler
│   ├── register.php            # Registration handler
│   └── secret.php              # Secret password handler
├── uploads/                    # User-uploaded images
├── views/
│   └── dashboard.php           # Agent dashboard
├── index.php                   # Home page
├── blog.php                    # Blog listing (categories, tags, pagination)
├── article.php                 # Single article view
├── login.php                   # Login page
├── register.php                # Registration page
├── secret.php                  # Classified access
├── logout.php                  # Logout
├── database.sql                # Full schema + 8 fan articles + sample data
└── README.md
```

## Blog CMS Features

### Article Editor
- TinyMCE 7 rich text editor (dark theme)
- Auto-generated slugs from title
- Featured image: upload, URL, or pick from assets
- Category selector with hierarchical display
- Dynamic tags (comma-separated, auto-created)
- Status: Draft / Published / Scheduled / Archived
- Schedule date picker for timed publishing

### SEO Settings
- SEO Title (separate from article title)
- Meta Description
- Open Graph Title & Description
- Live Google preview

### Multilingual Content
- Each article has a language (EN/FR/IT)
- Articles are linked via `translation_group`
- One-click "Create Translation" for missing languages
- Blog frontend filters by current UI language

### Roles
| Role   | Blog                   | Admin Panel |
|--------|------------------------|-------------|
| Agent  | Read only              | Own drafts  |
| Editor | Create, edit, publish  | Full access |
| Admin  | Everything             | Full access |

## Database Schema

- `users` - Accounts with roles (agent/editor/admin)
- `categories` - Hierarchical with `parent_id`
- `tags` - Flat tag system
- `articles` - Full blog with SEO, multilingual, scheduling
- `article_tags` - Many-to-many pivot
- `cases` - X-Files case management
- `evidence` - Case evidence files
- `case_notes` - Agent notes on cases

## Language Switching

Append `?lang=xx` to any URL: `?lang=en`, `?lang=fr`, `?lang=it`

Language is stored in session. Blog content is filtered by language.

## License

Educational project - for learning purposes only.
