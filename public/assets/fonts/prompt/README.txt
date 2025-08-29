Place Google Font files for Prompt here so the app can use them offline.

Recommended minimal set (WOFF2):
- Prompt-VariableFont_wght.woff2 (covers weights 100-900)
- Prompt-Italic-VariableFont_wght.woff2 (optional, italics)

Alternatively, static weights:
- Prompt-Regular.woff2 (400)
- Prompt-Medium.woff2 (500) [optional]
- Prompt-Bold.woff2 (700)
- Prompt-Italic.woff2 (400 italic) [optional]

You can download these from Google Fonts:
1) Go to https://fonts.google.com/specimen/Prompt
2) Click the Download family button.
3) Extract and copy the *.woff2 files into this folder.

After placing files, the app will automatically use them via @font-face configured in app/Views/layout.phtml.
