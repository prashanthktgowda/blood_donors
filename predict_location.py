import pickle
import sys
import subprocess
from sklearn.feature_extraction.text import CountVectorizer
from sklearn.linear_model import LinearRegression

# Ensure the address argument is passed
if len(sys.argv) < 2:
    print("Error: Please provide an address as an argument.")
    sys.exit(1)

# Get the input address
address = sys.argv[1]

# Download the model from Google Cloud Storage
bucket_name = 'newlife_frontend'
model_file_name = 'model_2.pkl'
local_model_path = f'./{model_file_name}'

# Use subprocess to download the model file
try:
    subprocess.run(['gsutil', 'cp', f'gs://{bucket_name}/{model_file_name}', local_model_path], check=True)
except subprocess.CalledProcessError as e:
    print(f"Error downloading the model file: {e}")
    sys.exit(1)

try:
    # Load the vectorizer
    with open('vectorizer_2.pkl', 'rb') as f:
        vectorizer = pickle.load(f)

    # Load the model
    with open(local_model_path, 'rb') as f:
        model = pickle.load(f)

    # Process the input address
    address_vector = vectorizer.transform([address])

    # Predict latitude and longitude
    predicted_coordinates = model.predict(address_vector)
    latitude, longitude = predicted_coordinates[0]

    # Output the result in a format that can be captured by PHP
    print(f"{latitude},{longitude}")

except Exception as e:
    print(f"Error: {e}")
