import os
import re

dir_path = r'c:\xampp\htdocs\enjoyfe_web\acciones'

def process_file(filepath):
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        encoding = 'utf-8'
    except UnicodeDecodeError:
        with open(filepath, 'r', encoding='utf-16') as f:
            content = f.read()
        encoding = 'utf-16'

    # Find the URL initialization line
    url_match_static = re.search(r'\$url\s*=\s*"http://127\.0\.0\.1:5000([^"]*)";', content)
    url_match_dynamic = re.search(r'\$url\s*=\s*"http://127\.0\.0\.1:5000([^"]*)"\s*\.\s*([^;]+);', content)
    
    if url_match_dynamic:
        endpoint_str = '"' + url_match_dynamic.group(1) + '" . ' + url_match_dynamic.group(2)
    elif url_match_static:
        endpoint_str = '"' + url_match_static.group(1) + '"'
    else:
        # Maybe it's directly assigned to a variable without the port?
        url_match_general = re.search(r'\$url\s*=\s*"http://127\.0\.0\.1:5000"\s*\.\s*([^;]+);', content)
        if url_match_general:
            endpoint_str = '"" . ' + url_match_general.group(1)
        else:
            return False
        
    # Check method
    method = '"GET"'
    if 'CURLOPT_POST, true' in content or "CURLOPT_CUSTOMREQUEST, 'POST'" in content or 'CURLOPT_CUSTOMREQUEST, "POST"' in content:
        method = '"POST"'
    elif 'CURLOPT_CUSTOMREQUEST, "PUT"' in content or "CURLOPT_CUSTOMREQUEST, 'PUT'" in content:
        method = '"PUT"'
    elif 'CURLOPT_CUSTOMREQUEST, "DELETE"' in content or "CURLOPT_CUSTOMREQUEST, 'DELETE'" in content:
        method = '"DELETE"'
        
    # Check payload
    payload = "null"
    payload_match = re.search(r'CURLOPT_POSTFIELDS\s*,\s*json_encode\(([^)]+)\)', content)
    if payload_match:
        payload = payload_match.group(1)
        
    # Find the entire cURL block to replace
    start_pattern = r'\$url\s*=\s*.*?;'
    end_pattern = r'\$resp_data\s*=\s*json_decode\(\$respuesta,\s*true\);'
    
    block_match = re.search(f'({start_pattern}.*?{end_pattern})', content, re.DOTALL)
    if not block_match:
        end_pattern2 = r'curl_close\(\$ch\);'
        block_match = re.search(f'({start_pattern}.*?{end_pattern2})', content, re.DOTALL)
        if not block_match:
            return False
            
    replacement = f"""require_once '../componentes/api_client.php';
$resultado_api = llamar_api({endpoint_str}, {method}, {payload});
$http_code = $resultado_api['http_code'];
$respuesta = $resultado_api['respuesta'];
$resp_data = $resultado_api['data'];"""

    # Evitar doble reemplazo si ya existe
    if 'llamar_api(' in content:
        return False

    new_content = content.replace(block_match.group(1), replacement)
    
    with open(filepath, 'w', encoding=encoding) as f:
        f.write(new_content)
    
    return True

for filename in os.listdir(dir_path):
    if filename.endswith('.php'):
        filepath = os.path.join(dir_path, filename)
        if process_file(filepath):
            print(f"Refactored {filename}")
        else:
            print(f"Skipped {filename} (no standard cURL block)")
