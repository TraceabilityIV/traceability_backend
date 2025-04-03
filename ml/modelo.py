import numpy as np
import pandas as pd
from sklearn.preprocessing import StandardScaler
from sklearn.ensemble import RandomForestClassifier
import joblib
import datetime
import traceback
from cultivos import CULTIVOS_PREDEFINIDOS
import psycopg2
import os
import requests
from datetime import datetime


class Modelo:

	def __init__(self):
		self.model = None
		self.scaler = None
		self.model_path = "/app/modelo.joblib"
		self.scaler_path = "/app/scaler.joblib"
		# Intentar cargar el modelo existente si está disponible
		self.cargar_modelo()

	def entrenar_modelo(self):
		"""Entrena el modelo con los datos proporcionados"""
		try:
			# Obtener cultivos de la base de datos
			cultivos = self.consultar_cultivos()
			
			# Preparar los datos de entrenamiento
			data = []
			labels = []
			for cultivo in cultivos:
				for temp in range(int(cultivo["temp_min"]), int(cultivo["temp_max"]) + 1):
					for ph in np.arange(cultivo["ph_min"], cultivo["ph_max"] + 0.1, 0.1):
						data.append([
							temp,
							ph,
							cultivo["precipitacion_min"],
							cultivo["profundidad_suelo"],
							hash(cultivo["textura_suelo"]) % 10
						])
						labels.append(cultivo["nombre"])

			X = np.array(data)
			y = np.array(labels)
			self.scaler = StandardScaler()
			X = self.scaler.fit_transform(X)
			self.model = RandomForestClassifier(n_estimators=500, random_state=42)
			self.model.fit(X, y)
			
			# Guardar el modelo y el scaler
			self.guardar_modelo()
			
		except Exception as e:
			print(f"Error durante el entrenamiento: {str(e)}")
			print(traceback.format_exc())
			raise

	def guardar_modelo(self):
		"""Guarda el modelo y el scaler en archivos separados"""
		try:
			if self.model is not None:
				joblib.dump(self.model, self.model_path)
				print(f"Modelo guardado en {self.model_path}")
			if self.scaler is not None:
				joblib.dump(self.scaler, self.scaler_path)
				print(f"Scaler guardado en {self.scaler_path}")
		except Exception as e:
			print(f"Error al guardar el modelo: {str(e)}")

	def cargar_modelo(self):
		"""Carga el modelo y el scaler si existen"""
		try:
			# Intentar cargar el modelo
			import os
			if os.path.exists(self.model_path) and os.path.exists(self.scaler_path):
				self.model = joblib.load(self.model_path)
				self.scaler = joblib.load(self.scaler_path)
				print(f"Modelo cargado desde {self.model_path}")
				return True
			else:
				print("No se encontró un modelo existente. Se creará uno nuevo.")
				return False
		except Exception as e:
			print(f"Error al cargar el modelo: {str(e)}")
			self.model = None
			self.scaler = None
			return False

	def get_db_connection(self):
		conn = psycopg2.connect(
			host=os.environ.get('DB_HOST', 'postgres'),
			database=os.environ.get('DB_NAME', 'traceability'),
			user=os.environ.get('DB_USER', 'root'),
			password=os.environ.get('DB_PASSWORD', 'i39f6^O+`8QI'),
			port=os.environ.get('DB_PORT', '5432')
		)
		return conn
	
	def consultar_cultivos(self):
		# Consulta de cultivos en la base de datos
		try:
			conn = self.get_db_connection()
			cur = conn.cursor()
			
			# Ejecutar la consulta para obtener todos los cultivos
			cur.execute('''
				SELECT nombre, temperatura_min as temp_min, temperatura_max as temp_max, ph_min, ph_max, precipitacion_min, profundidad_suelo, textura_suelo
				FROM cultivos_predefinidos
			''')
			
			# Obtener los resultados
			cultivos_db = cur.fetchall()
			
			# Cerrar cursor y conexión
			cur.close()
			conn.close()
			
			# Formatear los resultados como una lista de diccionarios
			cultivos_list = []
			for cultivo in cultivos_db:
				cultivos_list.append({
					'nombre': cultivo[0],
					'temp_min': float(cultivo[1] or 0),
					'temp_max': float(cultivo[2] or 0),
					'ph_min': float(cultivo[3] or 0),
					'ph_max': float(cultivo[4] or 0),
					'precipitacion_min': float(cultivo[5] or 0),
					'profundidad_suelo': float(cultivo[6] or 0),
					'textura_suelo': cultivo[7]
				})
			
			# Si no hay cultivos en la base de datos, usar los predefinidos
			if not cultivos_list:
				cultivos_list = CULTIVOS_PREDEFINIDOS
				
			return cultivos_list
		
		except Exception as e:
			# En caso de error, devolver los cultivos predefinidos
			return CULTIVOS_PREDEFINIDOS

	def obtener_datos_climaticos(self, latitude, longitude):
		"""
		Obtiene datos ambientales (clima y suelo) para una ubicación específica.
		
		Args:
			latitude (float): Latitud de la ubicación
			longitude (float): Longitud de la ubicación
			
		Returns:
			dict: Diccionario con datos de temperatura, precipitación y suelo
		"""
		# Inicializar diccionario de resultados
		result = {
			"temp_min": None,
			"temp_max": None,
			"ph": None,
			"precipitacion": None,
			"profundidad_suelo": None,
			"textura_suelo": None
		}
		
		try:
			# 1. Obtener datos de temperatura y precipitación de Open-Meteo
			weather_url = f"https://archive-api.open-meteo.com/v1/archive?latitude={latitude}&longitude={longitude}&start_date=2023-01-01&end_date=2023-12-31&daily=temperature_2m_max,temperature_2m_min,precipitation_sum&timezone=auto"
			weather_response = requests.get(weather_url)
			
			if weather_response.status_code == 200:
				weather_data = weather_response.json()
				
				# Calcular temperaturas promedio anuales
				if 'daily' in weather_data:
					temp_min_values = weather_data['daily']['temperature_2m_min']
					temp_max_values = weather_data['daily']['temperature_2m_max']
					precip_values = weather_data['daily']['precipitation_sum']
					
					# Calcular promedios para los valores disponibles
					if temp_min_values:
						result["temp_min"] = round(statistics.mean([t for t in temp_min_values if t is not None]))
					if temp_max_values:
						result["temp_max"] = round(statistics.mean([t for t in temp_max_values if t is not None]))
					if precip_values:
						result["precipitacion"] = round(sum([p for p in precip_values if p is not None]))
			
			# 2. Obtener datos de suelo de SoilGrids API
			soilgrids_url = f"https://rest.isric.org/soilgrids/v2.0/properties/query?lon={longitude}&lat={latitude}"
			soil_response = requests.get(soilgrids_url)
			
			if soil_response.status_code == 200:
				soil_data = soil_response.json()
				
				# Extraer datos de pH del suelo
				if 'properties' in soil_data and 'phh2o' in soil_data['properties'] and 'layers' in soil_data['properties']['phh2o']:
					ph_layers = soil_data['properties']['phh2o']['layers']
					if ph_layers:
						# Tomar el pH de la capa superficial (0-5cm)
						surface_ph = next((layer['mean'] for layer in ph_layers if layer['name'] == 'sl1'), None)
						if surface_ph:
							# SoilGrids pH está multiplicado por 10, dividir para obtener valor real
							result["ph"] = round(surface_ph / 10, 1)
				
				# Extraer datos de profundidad del suelo (usamos bdticm: profundidad hasta la roca)
				if 'properties' in soil_data and 'bdticm' in soil_data['properties']:
					result["profundidad_suelo"] = soil_data['properties']['bdticm'].get('mean', 100)
				
				# Determinar textura del suelo basada en contenido de arcilla, arena y limo
				clay_percent = None
				sand_percent = None
				silt_percent = None
				
				# Obtener porcentaje de arcilla
				if 'properties' in soil_data and 'clay' in soil_data['properties'] and 'layers' in soil_data['properties']['clay']:
					clay_layers = soil_data['properties']['clay']['layers']
					if clay_layers:
						surface_clay = next((layer['mean'] for layer in clay_layers if layer['name'] == 'sl1'), None)
						if surface_clay:
							clay_percent = surface_clay / 10  # Convertir de g/kg a porcentaje
				
				# Obtener porcentaje de arena
				if 'properties' in soil_data and 'sand' in soil_data['properties'] and 'layers' in soil_data['properties']['sand']:
					sand_layers = soil_data['properties']['sand']['layers']
					if sand_layers:
						surface_sand = next((layer['mean'] for layer in sand_layers if layer['name'] == 'sl1'), None)
						if surface_sand:
							sand_percent = surface_sand / 10  # Convertir de g/kg a porcentaje
				
				# Obtener porcentaje de limo (o calcular como resto)
				if 'properties' in soil_data and 'silt' in soil_data['properties'] and 'layers' in soil_data['properties']['silt']:
					silt_layers = soil_data['properties']['silt']['layers']
					if silt_layers:
						surface_silt = next((layer['mean'] for layer in silt_layers if layer['name'] == 'sl1'), None)
						if surface_silt:
							silt_percent = surface_silt / 10  # Convertir de g/kg a porcentaje
				
				# Si tenemos arcilla y arena pero no limo, calcular limo
				if clay_percent is not None and sand_percent is not None and silt_percent is None:
					silt_percent = 100 - (clay_percent + sand_percent)
				
				# Clasificación de textura basada en los tipos proporcionados
				if clay_percent is not None and sand_percent is not None:
					if clay_percent >= 40:
						result["textura_suelo"] = "arcilloso"
					elif sand_percent >= 85:
						result["textura_suelo"] = "arenoso"
					elif silt_percent and silt_percent >= 80:
						result["textura_suelo"] = "limoso"
					elif sand_percent >= 70 and clay_percent <= 15:
						result["textura_suelo"] = "franco-arenoso"
					elif clay_percent >= 27 and clay_percent < 40:
						result["textura_suelo"] = "franco-arcilloso"
					else:
						result["textura_suelo"] = "franco"
			
			# Si faltan datos, usar valores predeterminados razonables
			if result["ph"] is None:
				result["ph"] = 6.5  # pH neutro como valor predeterminado
			if result["profundidad_suelo"] is None:
				result["profundidad_suelo"] = 100  # 100 cm como valor predeterminado
			if result["textura_suelo"] is None:
				result["textura_suelo"] = "franco"  # Textura franca como valor predeterminado
				
		except Exception as e:
			print(f"Error al obtener datos: {e}")
			# Devolver valores predeterminados en caso de error
			return {
				"temp_min": 22,
				"temp_max": 28,
				"ph": 5.5,
				"precipitacion": 1000,
				"profundidad_suelo": 100,
				"textura_suelo": "franco"
			}
		
		return result