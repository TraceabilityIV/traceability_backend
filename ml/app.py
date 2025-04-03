from flask import Flask, request, jsonify
import psycopg2
import os
import numpy as np
import traceback
from cultivos import CULTIVOS_PREDEFINIDOS
from modelo import Modelo

app = Flask(__name__)

#prueba get
@app.route('/', methods=['GET'])
def get():
    return jsonify({'message': 'Servicio ML listo para implementar predicciones'})

@app.route('/predict', methods=['POST'])
def predict():
	try:
		# Obtener datos de la solicitud
		data = request.get_json()

		modelo = Modelo()

		if modelo.model is None or modelo.scaler is None:
			return jsonify({'error': 'El modelo o el scaler no se cargaron correctamente'}), 500

		datos_climaticos = modelo.obtener_datos_climaticos(data["lat"], data["long"])

		temp_prom = (datos_climaticos["temp_min"] + datos_climaticos["temp_max"]) / 2

		entrada = np.array([
			temp_prom,
			datos_climaticos["ph"],
			datos_climaticos["precipitacion"],
			datos_climaticos["profundidad_suelo"],
			hash(datos_climaticos["textura_suelo"]) % 10
		]).reshape(1, -1)

		entrada = modelo.scaler.transform(entrada)

		predicciones = modelo.model.predict_proba(entrada)[0]

		recomendaciones = []

		for i, cultivo in enumerate(modelo.model.classes_):
			compatibilidad = predicciones[i]
			if compatibilidad <= 0:  # Ajustar el umbral para incluir más predicciones
				continue

			recomendaciones.append((cultivo, compatibilidad))

		recomendaciones.sort(key=lambda x: x[1], reverse=True)

		return jsonify({
			"recomendaciones": recomendaciones,
			"datos_climaticos": datos_climaticos
		})
		
	except Exception as e:
		error_message = str(e)
		print(f"Error al obtener recomendaciones: {error_message}")
		return jsonify({'error': f'Error al obtener recomendaciones: {error_message}'}), 500

@app.route('/train', methods=['POST'])
def train():
    try:
        # Crear instancia del modelo y entrenar
        modelo = Modelo()
        modelo.entrenar_modelo()
        return jsonify({'message': 'Modelo entrenado correctamente'})
    except Exception as e:
        # Capturar cualquier error y devolverlo como respuesta
        error_message = str(e)
        # Imprimir el traceback completo para depuración
        print(f"Error al entrenar el modelo: {error_message}")
        print(traceback.format_exc())
        return jsonify({'error': f'Error al entrenar el modelo: {error_message}'}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
