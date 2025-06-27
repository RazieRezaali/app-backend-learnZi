from flask import Flask, request, send_file
from gtts import gTTS
import os
from io import BytesIO
from flask_cors import CORS, cross_origin

app = Flask(__name__)
cors = CORS(app)
app.config['CORS_HEADERS'] = 'Content-Type'

@app.route("/speak", methods=["POST"])
@cross_origin()
def speak():
    data = request.json
    char = data.get("character", "")
    if not char:
        return {"error": "No character provided"}, 400

    tts = gTTS(char, lang='zh-CN')
    audio_fp = BytesIO()
    tts.write_to_fp(audio_fp)
    audio_fp.seek(0)

    return send_file(audio_fp, mimetype="audio/mpeg")

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5001)