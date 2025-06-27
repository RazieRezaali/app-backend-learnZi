from flask import Flask, request, jsonify
from paddleocr import PaddleOCR
import numpy as np
from PIL import Image
import io
import logging
from flask_cors import CORS

app = Flask(__name__)
CORS(app, resources={r"/upload": {"origins": "*"}})
logging.basicConfig(level=logging.DEBUG)

@app.route('/upload', methods=['POST'])
def ocr_api():
    ocr = PaddleOCR(use_angle_cls=True, lang='ch', show_log=False)
    try:        
        if 'image' not in request.files:
            return jsonify({"error": "No image file provided"}), 400
        image_file = request.files['image']
        app.logger.debug(f"Received file: {image_file.filename}")
        image_bytes = image_file.read()
        image = Image.open(io.BytesIO(image_bytes)).convert('RGB')
        #image.save("debug_uploaded.png")
        image_np = np.array(image)
        app.logger.debug(f"Image shape: {image_np.shape}")

        result = ocr.ocr(image_np, cls=True)
        app.logger.debug(f"OCR result: {result}")
        recognized_text = [line[1][0] for line in result[0]] if result and result[0] else []

        return jsonify({"text": recognized_text})
    except Exception as e:
        app.logger.error(f"Error during OCR processing: {e}")
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
