import re

with open('dashboard.php', 'r', encoding='utf-8') as f:
    content = f.read()

script_pattern = re.compile(r'<script>(.*?)</script>', re.DOTALL)
match = script_pattern.search(content)

if match:
    js_content = match.group(1).strip()
    
    # Replace PHP blocks with JS conditions
    js_content = js_content.replace('<?php if (!$es_admin): ?>', 'if (!window.EnjoyfeConfig.esAdmin) {')
    js_content = js_content.replace('<?php endif; ?>', '}')
    
    with open('recursos/js/dashboard.js', 'w', encoding='utf-8') as f:
        f.write(js_content)
        
    replacement = '''<script>
    window.EnjoyfeConfig = window.EnjoyfeConfig || {};
    window.EnjoyfeConfig.esAdmin = <?php echo ($es_admin) ? 'true' : 'false'; ?>;
</script>
<script src="recursos/js/dashboard.js"></script>'''

    new_content = content[:match.start()] + replacement + content[match.end():]
    
    with open('dashboard.php', 'w', encoding='utf-8') as f:
        f.write(new_content)
    print('dashboard.php extracted.')
else:
    print('Regex failed for dashboard.php')
