import pickle
import sys
from sklearn.feature_extraction.text import CountVectorizer
from sklearn.linear_model import LinearRegression

# Ensure the address argument is passed
if len(sys.argv) <1:
    print("Error: Please provide an address as an argument.")
    sys.exit(1)

# Get the input address
address = sys.argv[1]
try:
    # Load the vectorizer and the model
    with open('vectorizer.pkl', 'rb') as f:
        vectorizer = pickle.load(f)

    with open('model.pkl', 'rb') as f:
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
