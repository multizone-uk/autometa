# AutoMeta Project Patterns and Knowledge

This file documents important patterns, architectural decisions, and Joomla-specific quirks for the AutoMeta package.

## Package Architecture

### Unified Package Structure
- **Decision**: AutoMeta uses a unified package approach with centralized updates
- **Why**: Prevents version mismatches between component and plugin
- **Implementation**:
  - Only `pkg_autometa.xml` has `<updateservers>` and `<changelogurl>`
  - Component and plugin manifests have NO update servers
  - Users can only update through the package (not individual extensions)

### Version Synchronization
- **Source of Truth**: Plugin manifest (`plg_content_autometa/autometa.xml`)
- All three manifests (plugin, component, package) must have identical version numbers
- Build script reads version from plugin manifest and applies to all

## Joomla Changelog System

### Critical Differences: Package vs Component/Plugin

#### Component/Plugin Changelogs
- **Storage**: Bundled inside the extension zip as `changelog.xml`
- **Location**: Installed alongside the manifest in the extension directory
- **Display**: Joomla reads from the local installed file
- **Format**: `<element>` matches the extension element (e.g., `com_autometa`, `autometa`)

#### Package Changelogs
- **Storage**: Hosted on remote update server
- **Fetch Method**: Loaded via AJAX from `<changelogurl>` when clicking version link
- **Critical**: File MUST be accessible at the remote URL or changelog will be empty
- **Format**: `<element>` matches `<packagename>` (e.g., `autometa`, NOT `pkg_autometa`)
- **Note**: While `changelog.xml` is bundled in package zip for reference, Joomla doesn't read it

### Changelog Element Naming
```xml
<!-- Plugin changelog -->
<element>autometa</element>  <!-- matches plugin element -->

<!-- Component changelog -->
<element>com_autometa</element>  <!-- matches component element -->

<!-- Package changelog -->
<element>autometa</element>  <!-- matches <packagename>, NOT pkg_autometa -->
```

## Build and Release Process

### Build Script (`build.sh`)
1. Extracts version from plugin manifest
2. Builds individual extension zips (plugin, component)
3. Builds unified package containing both extensions
4. Generates update server XMLs with SHA checksums
5. Copies changelogs to dist/ for upload
6. Optionally uploads to remote server via SSH

### Required Files on Update Server
For package updates to work properly, these files must be accessible:
- `pkg_autometa.xml` - Update server manifest with download URL and checksums
- `pkg_autometa-changelog.xml` - Changelog fetched via AJAX
- `pkg_autometa-{version}.zip` - The actual package file
- `pkg_autometa-latest.zip` - Convenience symlink to latest version

### Upload Configuration
Set these environment variables before running `build.sh` for automatic upload:
```bash
export SSH_USER="youruser"
export SSH_HOST="yourserver.com"
export REMOTE_PATH="/path/to/updates/directory"
export UPDATE_SERVER="https://yourserver.com/updates"
```

## Version Bumping Checklist

When releasing a new version:

1. **Update Version Numbers**:
   - [ ] `plg_content_autometa/autometa.xml` - `<version>`
   - [ ] `com_content_autometa/manifest.xml` - `<version>`
   - [ ] `pkg_autometa.xml` - `<version>`

2. **Update Changelogs**:
   - [ ] `plg_content_autometa/changelog.xml` - Add new `<changelog>` entry
   - [ ] `com_content_autometa/changelog.xml` - Add new `<changelog>` entry
   - [ ] `pkg_autometa_changelog.xml` - Add new `<changelog>` entry

3. **Build and Deploy**:
   - [ ] Run `./build.sh`
   - [ ] Upload files to update server (or configure SSH for automatic upload)
   - [ ] Verify changelog accessible at `<changelogurl>`
   - [ ] Test update in Joomla admin

## Common Issues and Solutions

### Empty Changelog Dialog in Joomla
**Symptom**: Clicking version link shows title but no content

**Common Causes**:
1. Changelog file not uploaded to remote server
2. Incorrect `<element>` in changelog (must match `<packagename>`)
3. Inaccessible `<changelogurl>` (403, 404, CORS issues)

**Solution**: Verify file is accessible at the exact URL in `<changelogurl>`

### Package Installation Error: "changelog.xml"
**Symptom**: "Error installing package: changelog.xml" or "Install path does not exist"

**Cause**: Attempted to add `<filename>changelog.xml</filename>` to package manifest `<files>` section

**Why This Fails**: The `<files>` section is only for sub-extensions (components, plugins, modules), not regular files

**Solution**: Remove changelog from `<files>` section; it's handled separately via `<changelogurl>`

### Version Mismatch After Update
**Symptom**: Component and plugin show different versions

**Cause**: Individual update servers still present in sub-extension manifests

**Solution**: Ensure component and plugin manifests have NO `<updateservers>` or `<changelogurl>` tags

## File Structure Reference

```
autometa/
├── .claude/
│   └── patterns.md                    # This file
├── plg_content_autometa/
│   ├── autometa.xml                   # Plugin manifest (NO updateservers)
│   └── changelog.xml                  # Bundled plugin changelog
├── com_content_autometa/
│   ├── manifest.xml                   # Component manifest (NO updateservers)
│   └── changelog.xml                  # Bundled component changelog
├── pkg_autometa.xml                   # Package manifest (HAS updateservers)
├── pkg_autometa_changelog.xml         # Package changelog source
├── build.sh                           # Build and deployment script
└── dist/                              # Generated by build script
    ├── pkg_autometa-{version}.zip
    ├── pkg_autometa.xml               # Update server manifest
    ├── pkg_autometa-changelog.xml     # For remote upload
    ├── autometa-{version}.zip
    ├── com_autometa-{version}.zip
    └── *.xml                          # Various update manifests
```

## Joomla Extension Installation Paths

After installation, files are located at:
- **Package manifest**: `administrator/manifests/packages/pkg_autometa.xml`
- **Component**: `administrator/components/com_autometa/`
- **Plugin**: `plugins/content/autometa/`

Note: Package `changelog.xml` is NOT extracted to installation path; must be fetched from remote URL.

## References

- [Joomla Update Server Documentation](https://docs.joomla.org/Deploying_an_Update_Server)
- [Package Changelog Example: pkg_switcheditor](https://github.com/conseilgouz/pkg_switcheditor_j4)
- Build script comments for detailed implementation notes
