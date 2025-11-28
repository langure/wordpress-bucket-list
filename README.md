# WordPress Bucket List Plugin

## Development Setup

This guide will help you set up a local development environment for contributing to this plugin.

---

### Prerequisites

- [Local WP](https://localwp.com/) — Local WordPress development environment
- [Visual Studio Code](https://code.visualstudio.com/) or your preferred IDE
- Git

---

### Setup Instructions

#### 1. Install Local WP

Download and install Local WP from [localwp.com](https://localwp.com/).  
Create a new WordPress site for development.

#### 2. Clone the Repository

Navigate to your projects directory:

```sh
cd /path/to/your/projects
```

Clone the repository:

```sh
git clone https://github.com/langure/wordpress-bucket-list.git
```

#### 3. Create Symlink to WordPress Plugins Directory

**macOS/Linux:**

Find your Local WP site path (usually something like):

```
~/Local Sites/your-site-name/app/public/wp-content/plugins/
```

Create symlink:

```sh
ln -s /path/to/your/projects/wordpress-bucket-list /path/to/local-wp-site/app/public/wp-content/plugins/wordpress-bucket-list
```

**Windows (Command Prompt as Administrator):**

```cmd
mklink /D "C:\path\to\local-wp-site\app\public\wp-content\plugins\wordpress-bucket-list" "C:\path\to\your\projects\wordpress-bucket-list"
```

**Windows (PowerShell as Administrator):**

```powershell
New-Item -ItemType SymbolicLink -Path "C:\path\to\local-wp-site\app\public\wp-content\plugins\wordpress-bucket-list" -Target "C:\path\to\your\projects\wordpress-bucket-list"
```

---

#### 4. Verify Setup

1. Check that the symlink works (should show your plugin files):

   ```sh
   ls /path/to/local-wp-site/app/public/wp-content/plugins/wordpress-bucket-list/
   ```

2. Open your WordPress admin panel in Local WP.
3. Navigate to **Plugins → Installed Plugins**.
4. You should see **"WordPress Bucket List"** in the list.
5. Activate the plugin.

---

### Development Workflow

1. Open the project in VS Code:

   ```sh
   code /path/to/your/projects/wordpress-bucket-list
   ```

2. Make changes to the plugin files in VS Code.
3. Changes are immediately reflected in your Local WP installation due to the symlink.
4. Test your changes in the WordPress admin or frontend.
5. Commit and push your changes:

   ```sh
   git add .
   git commit -m "Your commit message"
   git push origin main
   ```

---

### Plugin Structure

```
wordpress-bucket-list/
├── wordpress-bucket-list.php   # Main plugin file
├── src/                       # Source code
├── assets/                    # CSS/JS files
├── README.md                  # This file
└── .gitignore                 # Git ignore rules
```

---

### Troubleshooting

#### Plugin Not Appearing in WordPress Admin

1. Verify symlink exists:

   ```sh
   ls -la /path/to/local-wp-site/app/public/wp-content/plugins/
   ```

2. Check symlink target:

   - The symlink should point to your cloned repository.
   - Verify the repository contains `wordpress-bucket-list.php`.

3. Restart Local WP — Sometimes Local WP needs a restart to detect new plugins.
4. Check file permissions (macOS/Linux):

   ```sh
   chmod -R 755 /path/to/your/projects/wordpress-bucket-list
   ```

#### Symlink Issues on Windows

- Ensure you're running Command Prompt or PowerShell as Administrator.
- Windows may require Developer Mode to be enabled for symlinks to work properly.
- **Alternative:** Copy files instead of symlinking (manual sync required).

#### Finding Your Local WP Path

- **macOS/Linux:**  
  `~/Local Sites/your-site-name/app/public/wp-content/plugins/`
- **Windows:**  
  `C:\Users\{username}\Local Sites\your-site-name\app\public\wp-content\plugins\`

---

### Contributing

1. Fork the repository.
2. Create a feature branch:

   ```sh
   git checkout -b feature-name
   ```

3. Make your changes following the setup above.
4. Test thoroughly in your Local WP environment.
5. Commit your changes:

   ```sh
   git commit -m "Add new feature"
   ```

6. Push to your fork:

   ```sh
   git push origin feature-name
   ```

7. Create a Pull Request.

---

### WordPress Development Resources

- [WordPress Plugin Developer Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [Local WP Documentation](https://localwp.com/help-docs/)
