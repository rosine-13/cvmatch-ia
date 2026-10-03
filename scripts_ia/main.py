# main.py - Le microservice IA avec Groq et .env
import os
import json  # ✅ AJOUT
from pathlib import Path
from dotenv import load_dotenv

from flask import Flask, request, jsonify
import mysql.connector
import pdfplumber
from groq import Groq

# ============================================
# Chargement du fichier .env
# ============================================
env_path = Path(__file__).parent.parent / '.env'
load_dotenv(dotenv_path=env_path)

# ============================================
# Configuration
# ============================================
db_config = {
    'host': os.getenv('DB_HOST', 'localhost'),
    'user': os.getenv('DB_USER', 'root'),
    'password': os.getenv('DB_PASS', ''),
    'database': os.getenv('DB_NAME', 'cvmatchia_db')
}

GROQ_API_KEY = os.getenv('GROQ_API_KEY')

if not GROQ_API_KEY:
    print("⚠️ ERREUR : GROQ_API_KEY non trouvée dans le fichier .env")
    exit(1)

# Initialisation du client Groq
client = Groq(api_key=GROQ_API_KEY)

app = Flask(__name__)

# ============================================
# PROMPT EXPERT
# ============================================
SYSTEM_PROMPT = """Tu es un expert senior en recrutement avec 15 ans d'expérience en analyse de CV et en matching candidat/poste.

Ta mission : évaluer avec RIGUEUR et OBJECTIVITÉ la pertinence d'un profil candidat par rapport à une recherche de poste.

========================================
PRINCIPES FONDAMENTAUX
========================================
1. OBJECTIVITÉ : Ne jamais inventer d'informations absentes du profil.
2. FACTUALITÉ : Ne te baser que sur ce qui est explicitement écrit.
3. NEUTRALITÉ : Ignorer tout critère discriminatoire.
4. LÉGALITÉ : Ne jamais pénaliser sur la base du genre, de l'origine ethnique, de la religion ou de l'état civil.

========================================
MÉTHODE D'ÉVALUATION (4 AXES)
========================================
AXE 1 — COMPÉTENCES (poids : 50%)
- Lister toutes les compétences demandées.
- Vérifier leur présence dans le profil.
- Compétence impérative manquante → impact majeur
- Compétence souhaitée manquante → impact modéré
- Si 0 compétence correspondante → score plafonné à 25.

AXE 2 — EXPÉRIENCE (poids : 25%)
- Comparer les années d'expérience.
- Si le poste exige un minimum et que le candidat est en dessous :
  * Écart de 1-2 ans : pénalité modérée
  * Écart > 2 ans : pénalité forte
- Un profil plus expérimenté n'est PAS pénalisé.

AXE 3 — LOCALISATION (poids : 15%)
- Si une ville est mentionnée : bonus si même ville, pénalité sinon.
- Si aucune localisation demandée → ne pas pénaliser.

AXE 4 — FORMATION & SECTEUR (poids : 10%)
- Formation alignée → bonus
- Expérience dans le même secteur → bonus

========================================
BARÈME
========================================
- 90-100 : Profil idéal
- 75-89  : Très bon profil
- 55-74  : Bon profil
- 35-54  : Profil moyen
- 15-34  : Profil faible
- 0-14   : Hors sujet

========================================
RECOMMANDATION
========================================
- Score ≥ 75 : "fort recommandé"
- Score 55-74 : "recommandé"
- Score 35-54 : "à considérer"
- Score < 35 : "non recommandé"

========================================
FORMAT DE SORTIE (JSON STRICT)
========================================
Retourne UNIQUEMENT un JSON valide :

{
  "score": <entier 0-100>,
  "justification": "<1 phrase courte, 20 mots max>",
  "points_forts": ["<atout 1>", "<atout 2>"],
  "points_faibles": ["<lacune 1>"]
}

========================================
CONTRAINTES STRICTES
========================================
- justification : UNE SEULE phrase, maximum 20 mots.
- points_forts : MAXIMUM 2 éléments.
- points_faibles : MAXIMUM 1 élément.
- Sois direct et concis.
- Ne mentionne JAMAIS que tu es une IA.
- Parle comme un recruteur humain.
- Aucun texte en dehors du JSON."""

# ============================================
# FONCTIONS
# ============================================
def extract_text_from_pdf(file_path):
    """Extrait le texte d'un PDF"""
    text = ""
    try:
        with pdfplumber.open(file_path) as pdf:
            for page in pdf.pages:
                page_text = page.extract_text()
                if page_text:
                    text += page_text + "\n"
    except Exception as e:
        print(f"Erreur extraction PDF : {e}")
    return text


def analyze_with_groq(cv_text, job_description):
    """Envoie le CV et l'offre à l'API Groq"""
    try:
        response = client.chat.completions.create(
            model="openai/gpt-oss-120b",
            messages=[
                {"role": "system", "content": SYSTEM_PROMPT},
                {"role": "user", "content": f"REQUÊTE: {job_description}\n\nPROFIL CANDIDAT: {cv_text}"}
            ],
            temperature=0.2,
            response_format={"type": "json_object"}
        )
        result_text = response.choices[0].message.content
        return json.loads(result_text)
    except Exception as e:
        print(f"Erreur Groq : {e}")
        return {
            "score": 0,
            "justification": "Erreur lors de l'analyse IA.",
            "recommandation": "non recommandé"
        }


# ============================================
# ROUTE /process_cv
# ============================================
@app.route('/process_cv', methods=['POST'])
def process_cv():
    """Extrait le texte d'un CV et le stocke en base"""
    data = request.json
    cv_id = data.get('cv_id')
    file_path = data.get('file_path')

    if not cv_id or not file_path:
        return jsonify({"status": "error", "message": "cv_id et file_path requis"}), 400

    try:
        extracted_text = extract_text_from_pdf("../" + file_path)

        if not extracted_text:
            return jsonify({"status": "error", "message": "Aucun texte extrait du PDF"}), 400

        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        query = "UPDATE cvs SET extracted_text = %s, status = 'Analysé' WHERE id = %s"
        cursor.execute(query, (extracted_text, cv_id))
        conn.commit()
        cursor.close()
        conn.close()

        return jsonify({
            "status": "success",
            "message": "CV analysé avec succès",
            "text_length": len(extracted_text)
        })
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500


# ============================================
# ROUTE /search
# ============================================
@app.route('/search', methods=['POST'])
def search():
    """Recherche libre avec analyse Groq"""
    data = request.json
    query = data.get('query')

    if not query:
        return jsonify({"status": "error", "message": "Requête vide"}), 400

    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT id, extracted_text FROM cvs WHERE extracted_text IS NOT NULL AND extracted_text != ''")
        cvs = cursor.fetchall()
        cursor.close()
        conn.close()

        results = []
        for cv in cvs:
            analysis = analyze_with_groq(cv['extracted_text'], query)
            score = analysis.get('score', 0)

            # Seuil : on ne garde que les candidats pertinents
            if score >= 50:
                results.append({
                    "cv_id": cv['id'],
                    "score": score,
                    "justification": analysis.get('justification', ''),
                    "points_forts": analysis.get('points_forts', []),
                    "points_faibles": analysis.get('points_faibles', [])
                })

        results.sort(key=lambda x: x['score'], reverse=True)

        return jsonify({
            "status": "success",
            "query": query,
            "total": len(results),
            "results": results
        })
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500


# ============================================
# ROUTE /match_all
# ============================================
@app.route('/match_all', methods=['POST'])
def match_all():
    """Compare TOUS les CV avec une offre"""
    data = request.json
    job_id = data.get('job_id')

    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)

        cursor.execute("SELECT title, description, required_skills FROM jobs WHERE id = %s", (job_id,))
        job_data = cursor.fetchone()

        if not job_data:
            cursor.close()
            conn.close()
            return jsonify({"status": "error", "message": "Offre introuvable"}), 404

        job_text = f"{job_data['title']} {job_data['description']} {job_data['required_skills']}"

        cursor.execute("SELECT id, extracted_text FROM cvs WHERE extracted_text IS NOT NULL AND extracted_text != ''")
        cvs = cursor.fetchall()

        cursor.close()
        conn.close()

        results = []
        for cv in cvs:
            analysis = analyze_with_groq(cv['extracted_text'], job_text)
            score = analysis.get('score', 0)

            if score >= 40:
                results.append({
                    "cv_id": cv['id'],
                    "score": score,
                    "justification": analysis.get('justification', ''),
                    "recommandation": analysis.get('recommandation', ''),
                    "points_forts": analysis.get('points_forts', []),
                    "points_faibles": analysis.get('points_faibles', []),
                    "competences_detectees": analysis.get('competences_detectees', [])
                })

        results.sort(key=lambda x: x['score'], reverse=True)

        return jsonify({
            "status": "success",
            "job_id": job_id,
            "total_cvs": len(results),
            "results": results
        })
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500


# ============================================
# LANCEMENT
# ============================================
if __name__ == '__main__':
    print("=" * 50)
    print("🤖 CVMatch IA - Microservice démarré")
    print("=" * 50)
    print(f"Clé Groq : {GROQ_API_KEY[:20]}..." if GROQ_API_KEY else "❌ Clé absente")
    print(f"Base de données : {db_config['database']}")
    print("=" * 50)
    app.run(port=5000, debug=True)