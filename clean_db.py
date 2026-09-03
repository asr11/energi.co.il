import re

def clean_sql(input_file, output_file):
    print(f"Cleansing SQL dump properly: {input_file} -> {output_file}")
    
    with open(input_file, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()

    # 1. Remove transient rows from wpw4_options
    # Transient options patterns in MySQL insert statements:
    # ('_transient_...', ...), ('_site_transient_...', ...)
    def clean_options(match):
        prefix = match.group(1)
        values_str = match.group(2)
        rows = values_str.split("),(")
        kept_rows = []
        for r in rows:
            if "_transient_" in r or "_site_transient_" in r or "action_scheduler" in r:
                continue
            kept_rows.append(r)
        if not kept_rows:
            return ""
        return prefix + "),(".join(kept_rows)

    # Clean options inserts
    content = re.sub(r"(INSERT INTO `?wpw4_options`? VALUES\s*\n?\()(.*?)(;\n)", clean_options, content, flags=re.DOTALL)
    content = re.sub(r"(INSERT INTO `?wp_options`? VALUES\s*\n?\()(.*?)(;\n)", clean_options, content, flags=re.DOTALL)

    # 2. Filter post revisions from wpw4_posts
    def clean_posts(match):
        prefix = match.group(1)
        values_str = match.group(2)
        rows = values_str.split("),(")
        kept_rows = []
        for r in rows:
            if "'revision'" in r or "'auto-draft'" in r:
                continue
            kept_rows.append(r)
        if not kept_rows:
            return ""
        return prefix + "),(".join(kept_rows)

    content = re.sub(r"(INSERT INTO `?wpw4_posts`? VALUES\s*\n?\()(.*?)(;\n)", clean_posts, content, flags=re.DOTALL)

    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(content)

    print("Cleansing complete!")

if __name__ == '__main__':
    clean_sql('backup/original_db.sql', 'backup/cleaned_db.sql')
