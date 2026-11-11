import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

def create_master_icon(size=1024, is_round=False):
    img = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    draw = ImageDraw.Draw(img)

    center = size / 2
    radius = size * 0.46

    # 1. Background Circle or Squircle
    bg = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    bg_draw = ImageDraw.Draw(bg)

    if is_round:
        bg_draw.ellipse([center - radius, center - radius, center + radius, center + radius], fill=(15, 23, 42, 255))
    else:
        # Smooth rounded rect
        corner_r = int(size * 0.22)
        bg_draw.rounded_rectangle([center - radius, center - radius, center + radius, center + radius], corner_r, fill=(15, 23, 42, 255))

    # Add gradient glow inside
    for i in range(int(radius), 0, -8):
        factor = i / radius
        # Midnight navy (15, 23, 42) to Deep royal indigo (30, 41, 79)
        r = int(30 * (1 - factor) + 15 * factor)
        g = int(58 * (1 - factor) + 23 * factor)
        b = int(138 * (1 - factor) + 42 * factor)
        if is_round:
            bg_draw.ellipse([center - i, center - i, center + i, center + i], fill=(r, g, b, 255))
        else:
            cur_r = int(corner_r * (i / radius))
            bg_draw.rounded_rectangle([center - i, center - i, center + i, center + i], cur_r, fill=(r, g, b, 255))

    img.alpha_composite(bg)

    # 2. Golden Ring Border
    ring = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    ring_draw = ImageDraw.Draw(ring)
    ring_width = int(size * 0.035)

    if is_round:
        ring_draw.ellipse([center - radius, center - radius, center + radius, center + radius], outline=(234, 179, 8, 255), width=ring_width)
        ring_draw.ellipse([center - radius + ring_width, center - radius + ring_width, center + radius - ring_width, center + radius - ring_width], outline=(250, 204, 21, 160), width=int(ring_width*0.3))
    else:
        corner_r = int(size * 0.22)
        ring_draw.rounded_rectangle([center - radius, center - radius, center + radius, center + radius], corner_r, outline=(234, 179, 8, 255), width=ring_width)
        ring_draw.rounded_rectangle([center - radius + ring_width, center - radius + ring_width, center + radius - ring_width, center + radius - ring_width], int(corner_r*0.85), outline=(250, 204, 21, 160), width=int(ring_width*0.3))

    img.alpha_composite(ring)

    # 3. Draw Stylized Open Bible in the Center
    bible_layer = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    b_draw = ImageDraw.Draw(bible_layer)

    # Bible dimensions
    bx = center
    by = center - size * 0.06
    bw = size * 0.32
    bh = size * 0.22

    # Left page & Right page (parchment cream)
    # Left page polygon
    left_page = [
        (bx, by + bh * 0.8),
        (bx - bw, by + bh),
        (bx - bw * 1.05, by - bh * 0.7),
        (bx, by - bh * 0.9)
    ]
    # Right page polygon
    right_page = [
        (bx, by + bh * 0.8),
        (bx + bw, by + bh),
        (bx + bw * 1.05, by - bh * 0.7),
        (bx, by - bh * 0.9)
    ]

    # Bible Cover / Spine Shadow
    cover_left = [
        (bx, by + bh * 0.9),
        (bx - bw * 1.08, by + bh * 1.1),
        (bx - bw * 1.12, by - bh * 0.65),
        (bx, by - bh * 0.85)
    ]
    cover_right = [
        (bx, by + bh * 0.9),
        (bx + bw * 1.08, by + bh * 1.1),
        (bx + bw * 1.12, by - bh * 0.65),
        (bx, by - bh * 0.85)
    ]
    b_draw.polygon(cover_left, fill=(180, 83, 9, 255))
    b_draw.polygon(cover_right, fill=(180, 83, 9, 255))

    # Pages (off-white / parchment)
    b_draw.polygon(left_page, fill=(248, 250, 252, 255))
    b_draw.polygon(right_page, fill=(241, 245, 249, 255))

    # Book spine line
    b_draw.line([(bx, by - bh * 0.9), (bx, by + bh * 0.85)], fill=(203, 213, 225, 255), width=int(size * 0.01))

    # Page text lines (subtle gold / slate lines representing holy scripture)
    for row in range(4):
        ly = by - bh * 0.45 + row * (bh * 0.28)
        # Left lines
        b_draw.line([(bx - bw * 0.85, ly + 5), (bx - bw * 0.2, ly)], fill=(203, 213, 225, 180), width=int(size * 0.008))
        # Right lines
        b_draw.line([(bx + bw * 0.2, ly), (bx + bw * 0.85, ly + 5)], fill=(203, 213, 225, 180), width=int(size * 0.008))

    # Red ribbon bookmark hanging out
    ribbon = [
        (bx - size * 0.015, by + bh * 0.8),
        (bx + size * 0.015, by + bh * 0.8),
        (bx + size * 0.02, by + bh * 1.3),
        (bx, by + bh * 1.2),
        (bx - size * 0.02, by + bh * 1.3)
    ]
    b_draw.polygon(ribbon, fill=(225, 29, 72, 255))

    # 4. Radiant Golden Cross floating above Bible
    cross_layer = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    c_draw = ImageDraw.Draw(cross_layer)
    cx = center
    cy = by - bh * 0.65
    cw = size * 0.035
    ch = size * 0.20

    # Cross glow
    glow = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    g_draw = ImageDraw.Draw(glow)
    g_draw.ellipse([cx - size * 0.14, cy - size * 0.08, cx + size * 0.14, cy + size * 0.20], fill=(234, 179, 8, 100))
    glow = glow.filter(ImageFilter.GaussianBlur(size * 0.03))
    cross_layer.alpha_composite(glow)

    # Vertical bar
    c_draw.rounded_rectangle([cx - cw/2, cy - ch/2, cx + cw/2, cy + ch/2], int(cw/3), fill=(253, 224, 71, 255))
    # Horizontal bar
    c_draw.rounded_rectangle([cx - ch * 0.38, cy - ch * 0.2, cx + ch * 0.38, cy - ch * 0.2 + cw], int(cw/3), fill=(253, 224, 71, 255))

    img.alpha_composite(bible_layer)
    img.alpha_composite(cross_layer)

    # 5. Bold Title Text: "BÍBLIA SHOW"
    text_layer = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    t_draw = ImageDraw.Draw(text_layer)

    font_path = "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"
    if not os.path.exists(font_path):
        font_path = "/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf"

    font_biblia = ImageFont.truetype(font_path, int(size * 0.11))
    font_show = ImageFont.truetype(font_path, int(size * 0.09))

    # Draw "BÍBLIA"
    text_biblia = "BÍBLIA"
    bbox_b = t_draw.textbbox((0, 0), text_biblia, font=font_biblia)
    tb_w = bbox_b[2] - bbox_b[0]
    tb_x = center - tb_w / 2
    tb_y = by + bh * 1.35

    # Shadow
    t_draw.text((tb_x + 3, tb_y + 4), text_biblia, font=font_biblia, fill=(0, 0, 0, 220))
    # Golden Gradient Text
    t_draw.text((tb_x, tb_y), text_biblia, font=font_biblia, fill=(250, 204, 21, 255))

    # Draw "SHOW" in cyan/white pill
    text_show = "SHOW 2.0"
    bbox_s = t_draw.textbbox((0, 0), text_show, font=font_show)
    ts_w = bbox_s[2] - bbox_s[0]
    ts_h = bbox_s[3] - bbox_s[1]
    ts_x = center - ts_w / 2
    ts_y = tb_y + size * 0.11

    # Show pill background
    pill_pad_x = size * 0.035
    pill_pad_y = size * 0.012
    t_draw.rounded_rectangle([
        ts_x - pill_pad_x, ts_y - pill_pad_y,
        ts_x + ts_w + pill_pad_x, ts_y + ts_h + pill_pad_y * 1.8
    ], int(size * 0.03), fill=(14, 165, 233, 255), outline=(255, 255, 255, 240), width=int(size * 0.006))

    # Show text
    t_draw.text((ts_x, ts_y), text_show, font=font_show, fill=(255, 255, 255, 255))

    img.alpha_composite(text_layer)
    return img

def main():
    print("[INFO] Gerando ícones mestre do Bíblia Show 2.0...")
    master_square = create_master_icon(1024, is_round=False)
    master_round = create_master_icon(1024, is_round=True)

    # 1. Electron Desktop Icons
    os.makedirs("electron/build/icons", exist_ok=True)
    icon_512 = master_square.resize((512, 512), Image.Resampling.LANCZOS)
    icon_512.save("electron/build/icon.png", "PNG")
    master_square.resize((256, 256), Image.Resampling.LANCZOS).save("electron/build/icon-256.png", "PNG")

    # Multi-resolution ICO for Windows
    ico_sizes = [(16, 16), (24, 24), (32, 32), (48, 48), (64, 64), (128, 128), (256, 256)]
    ico_images = [master_square.resize(s, Image.Resampling.LANCZOS) for s in ico_sizes]
    ico_images[0].save("electron/build/icon.ico", format="ICO", sizes=[s for s in ico_sizes], append_images=ico_images[1:])
    ico_images[0].save("public/favicon.ico", format="ICO", sizes=[(16, 16), (32, 32), (48, 48)], append_images=ico_images[1:3])
    print("[OK] Ícones de Desktop (icon.png, icon.ico) gerados com sucesso.")

    # Individual icon sizes for Linux desktop themes
    for sz in [16, 24, 32, 48, 64, 128, 256, 512]:
        master_square.resize((sz, sz), Image.Resampling.LANCZOS).save(f"electron/build/icons/{sz}x{sz}.png", "PNG")

    # 2. Public PWA & Web Icons
    os.makedirs("public/images", exist_ok=True)
    master_square.resize((192, 192), Image.Resampling.LANCZOS).save("public/images/icon-192.png", "PNG")
    master_square.resize((512, 512), Image.Resampling.LANCZOS).save("public/images/icon-512.png", "PNG")
    master_round.resize((192, 192), Image.Resampling.LANCZOS).save("public/images/icon-192-round.png", "PNG")
    master_round.resize((512, 512), Image.Resampling.LANCZOS).save("public/images/icon-512-round.png", "PNG")
    print("[OK] Ícones Web e PWA gerados em public/images/.")

    # 3. Android Native Mipmap Icons
    android_res = "android/app/src/main/res"
    density_map = {
        "mipmap-mdpi": 48,
        "mipmap-hdpi": 72,
        "mipmap-xhdpi": 96,
        "mipmap-xxhdpi": 144,
        "mipmap-xxxhdpi": 192
    }

    for folder, dim in density_map.items():
        target_dir = os.path.join(android_res, folder)
        os.makedirs(target_dir, exist_ok=True)
        # Standard launcher icon
        master_square.resize((dim, dim), Image.Resampling.LANCZOS).save(os.path.join(target_dir, "ic_launcher.png"), "PNG")
        # Round launcher icon
        master_round.resize((dim, dim), Image.Resampling.LANCZOS).save(os.path.join(target_dir, "ic_launcher_round.png"), "PNG")
        # Foreground icon (slightly smaller to fit adaptive icon circle)
        fg = master_square.resize((int(dim * 0.85), int(dim * 0.85)), Image.Resampling.LANCZOS)
        fg_canvas = Image.new("RGBA", (dim, dim), (0, 0, 0, 0))
        offset = int((dim - dim * 0.85) / 2)
        fg_canvas.paste(fg, (offset, offset), fg)
        fg_canvas.save(os.path.join(target_dir, "ic_launcher_foreground.png"), "PNG")

    print("[OK] Ícones Android mipmap gerados com sucesso.")

    # 4. Android Splash Screens
    splash_portrait = {
        "drawable-port-mdpi": (320, 480),
        "drawable-port-hdpi": (480, 800),
        "drawable-port-xhdpi": (720, 1280),
        "drawable-port-xxhdpi": (960, 1600),
        "drawable-port-xxxhdpi": (1280, 1920)
    }
    splash_landscape = {
        "drawable-land-mdpi": (480, 320),
        "drawable-land-hdpi": (800, 480),
        "drawable-land-xhdpi": (1280, 720),
        "drawable-land-xxhdpi": (1600, 960),
        "drawable-land-xxxhdpi": (1920, 1280)
    }

    def generate_splash(folder, width, height):
        target_dir = os.path.join(android_res, folder)
        os.makedirs(target_dir, exist_ok=True)
        splash = Image.new("RGBA", (width, height), (15, 23, 42, 255))
        # Place logo in center
        logo_dim = int(min(width, height) * 0.55)
        logo = master_square.resize((logo_dim, logo_dim), Image.Resampling.LANCZOS)
        lx = (width - logo_dim) // 2
        ly = (height - logo_dim) // 2
        splash.paste(logo, (lx, ly), logo)
        splash.save(os.path.join(target_dir, "splash.png"), "PNG")

    for f, (w, h) in splash_portrait.items():
        generate_splash(f, w, h)
    for f, (w, h) in splash_landscape.items():
        generate_splash(f, w, h)

    # General drawable splash
    generate_splash("drawable", 720, 1280)
    print("[OK] Telas de abertura (splash screens) Android geradas com sucesso.")

if __name__ == "__main__":
    main()
