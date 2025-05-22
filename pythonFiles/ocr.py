from flask import Flask, request, jsonify
from paddleocr import PaddleOCR
import numpy as np
from PIL import Image
import io
import logging
from flask_cors import CORS  # Import CORS

# Initialize Flask app
app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

# Configure logging
logging.basicConfig(level=logging.DEBUG)

# Initialize Flask app
app = Flask(__name__)
CORS(app, resources={r"/upload": {"origins": "*"}})  # Allow all origins for '/upload'

# Configure logging
logging.basicConfig(level=logging.DEBUG)

@app.route('/upload', methods=['POST'])
def ocr_api():
    # Initialize PaddleOCR
    ocr = PaddleOCR(use_angle_cls=True, lang='ch', show_log=False)
    try:        
        # Check if an image file is provided in the request
        if 'image' not in request.files:
            return jsonify({"error": "No image file provided"}), 400

        # Get the image file from the request
        image_file = request.files['image']

        # Log the received file
        app.logger.debug(f"Received file: {image_file.filename}")

        # Read the image file into bytes
        image_bytes = image_file.read()

        # Convert the image bytes to a NumPy array
        image = Image.open(io.BytesIO(image_bytes)).convert('RGB')
        image.save("debug_uploaded.png")
        image_np = np.array(image)

        # Log the image shape
        app.logger.debug(f"Image shape: {image_np.shape}")

        # Perform OCR on the image
        result = ocr.ocr(image_np, cls=True)

        # Log the OCR result
        app.logger.debug(f"OCR result: {result}")

        # Extract the recognized text
        recognized_text = [line[1][0] for line in result[0]] if result and result[0] else []

        # Return the recognized text as a JSON response
        return jsonify({"text": recognized_text})
    except Exception as e:
        # Log the error
        app.logger.error(f"Error during OCR processing: {e}")
        return jsonify({"error": str(e)}), 500

# Run the Flask app
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)

