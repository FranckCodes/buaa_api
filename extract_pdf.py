import pdfplumber

with pdfplumber.open('documents_modules/BUAA_Module_Utilisateur.pdf') as pdf:
    for page in pdf.pages:
        text = page.extract_text()
        if text:
            print(text)