import os

dir_path = r'c:\xampp\htdocs\enjoyfe_web\acciones'

def replace_in_file(filepath):
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()

    original_content = content
    
    # 1. Simple GET with token (like estadisticas_profesor)
    import re
    
    # Pattern for simple GET
    get_pattern = r'\$url\s*=\s*"http://127\.0\.0\.1:5000([^"]*)";\s*\$ch\s*=\s*curl_init\(\$url\);\s*curl_setopt\(\$ch,\s*CURLOPT_RETURNTRANSFER,\s*true\);\s*curl_setopt\(\$ch,\s*CURLOPT_HTTPHEADER,\s*\[\s*\'Authorization:\s*Bearer\s*\'\s*\.\s*\$_SESSION\[\'token\'\]\s*\]\);\s*\$([a-zA-Z0-9_]+)\s*=\s*curl_exec\(\$ch\);\s*\$([a-zA-Z0-9_]+)\s*=\s*curl_getinfo\(\$ch,\s*CURLINFO_HTTP_CODE\);\s*curl_close\(\$ch\);'
    
    def repl_get(match):
        endpoint = match.group(1)
        var_resp = match.group(2)
        var_code = match.group(3)
        return f"require_once '../componentes/api_client.php';\n    $__res = llamar_api(\"{endpoint}\");\n    ${var_resp} = $__res['respuesta'];\n    ${var_code} = $__res['http_code'];"

    content = re.sub(get_pattern, repl_get, content)
    
    # Pattern for simple DELETE
    delete_pattern = r'\$url\s*=\s*"http://127\.0\.0\.1:5000([^"]*)"\s*\.\s*\$id;\s*\$ch\s*=\s*curl_init\(\$url\);\s*curl_setopt\(\$ch,\s*CURLOPT_RETURNTRANSFER,\s*true\);\s*curl_setopt\(\$ch,\s*CURLOPT_CUSTOMREQUEST,\s*"DELETE"\);\s*curl_setopt\(\$ch,\s*CURLOPT_HTTPHEADER,\s*\[\s*\'Authorization:\s*Bearer\s*\'\s*\.\s*\$_SESSION\[\'token\'\],\s*\'Content-Type:\s*application/json\'\s*\]\);\s*\$([a-zA-Z0-9_]+)\s*=\s*curl_exec\(\$ch\);\s*\$([a-zA-Z0-9_]+)\s*=\s*curl_getinfo\(\$ch,\s*CURLINFO_HTTP_CODE\);\s*curl_close\(\$ch\);'
    
    def repl_delete(match):
        endpoint = match.group(1)
        var_resp = match.group(2)
        var_code = match.group(3)
        return f"require_once '../componentes/api_client.php';\n    $__res = llamar_api(\"{endpoint}\" . $id, \"DELETE\");\n    ${var_resp} = $__res['respuesta'];\n    ${var_code} = $__res['http_code'];"

    content = re.sub(delete_pattern, repl_delete, content)
    
    if content != original_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        return True
    return False

for filename in os.listdir(dir_path):
    if filename.endswith('.php'):
        filepath = os.path.join(dir_path, filename)
        if replace_in_file(filepath):
            print(f"Safely refactored {filename}")
