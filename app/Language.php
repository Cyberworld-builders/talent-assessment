<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	protected $fillable = [
		'name',
		'native_name',
		'code'
	];

	protected $translated_terms = [
		'es' => [
			'Create Your Profile' => 'Crea su perfil',
			'Please make sure that this information is accurate.' => 'Por favor asegúrese de que esta información este correcta.',
			'First Name' => 'Nombre',
			'Middle Name' => 'Segundo Nombre',
			'Last Name' => 'Apellido',
			'Email' => 'Correo electrónico/Email',
			'Password' => 'Contraseña',
			'New Password' => 'Nueva contraseña',
			'Confirm Password' => 'Confirme la contraseña',
			'Confirm New Password' => 'Confirme la nueva contraseña',
			'Hello' => 'Hola',
			'Logout' => 'Terminar la Sesión',
			'Research Questions' => 'Preguntas de la Investigación',
			'These questions are voluntary and will only be used for research purposes.' => 'Estas preguntas son voluntarias y sólo serán utilizadas para fines de la investigación.',
			'Age' => 'Edad',
			'Gender' => 'Sexo',
			'Male' => 'Masculino',
			'Female' => 'Femenino',
			'Ethnicity' => 'Etnicidad',
			'Industry' => 'Industria',
			'Select your gender...' => 'Selecciona tu genero...',
			'Select your ethnicity...' => 'Seleccione su raza...',
			'Select your industry...' => 'Seleccione su industria...',
			'Select your purpose for taking this assessment...' => 'Seleccione su proposito de la evaluacion...',
			'Assessment Purpose' => 'Propósito de la Evaluación',
			'Your Assignments' => 'Sus Tareas',
			'Assignment' => 'Tarea',
			'Completed' => 'Terminado',
			'Expiration' => 'Vencimiento',
			'Settings' => 'Ajustes',
			'Not Completed' => 'Incompleto',
			'Expired' => 'Vencido',
			'Expires in' => 'Vence en',
			'Take Assessment' => 'Tomar la Evaluación',
			'Continue Assessment' => 'Continuar la Evaluación',
			'Asian' => 'Asiático',
			'White' => 'Raza Blanca',
			'Black or African American' => 'Afroamericanos',
			'Hispanic or Latino' => 'Hispano o Latino',
			'Native American' => 'Nativo Americano',
			'Pacific Islander' => 'Islas del Pacifico',
			'Decline to Answer' => 'Negarse a Contestar',
			'Other' => 'Otro',
			'Advertising and Marketing' => 'La Publicidad y la Comercialización',
			'Banking and Financial Services' => 'Servicios Bancarios y Financieros',
			'Business Support Services' => 'Servicios de Apoyo Empresariales',
			'Construction' => 'Construcción',
			'Education' => 'Educación',
			'Energy, Utilities, and Telecommunications' => 'Energía, Utilidades, y Telecomunicaciones',
			'Entertainment and Media' => 'Entretenimiento y Medios de Comunicación',
			'Food and Beverage' => 'Alimentos y Bebidas',
			'Government' => 'Gobierno',
			'Health Care' => 'Cuidado de la Salud',
			'Industrial Metals and Mining' => 'Metales Industriales y Minería',
			'Information Technology' => 'Informatica / Technologia de la Informacion',
			'Law Enforcement' => 'Fuerza Judicial',
			'Leisure and Hospitality' => 'Hospitalidad',
			'Manufacturing' => 'Manufactura',
			'Pharmaceuticals' => 'Productos Farmacéuticos',
			'Retail Sales' => 'Ventas',
			'Sports and Recreation' => 'Deportes y Recreación',
			'Transportation' => 'Transporte',
			'Applying for a job' => 'Solicitando trabajo',
			'For my current employer' => 'Para Mi Empleador Actual',
			'Next' => 'Siguiente',
			'Update Profile' => 'Actualizar perfil',
			'Select Your Language' => 'Elija su idioma',
			'Language' => 'Idioma',
			'Continue' => 'Continuar',
			'You have no assessments assigned to you.' => 'Usted no tiene evaluaciones asignadas.',
			'Begin The Assessment' => 'Comenzar la Evaluación',
			'Questions' => 'Preguntas',
			'Submit Answers' => 'Enviar Respuestas',
			'This assessment has been completed!' => 'La evaluación esta completa!',
			'Thank you for participating, your answers have been recorded and a confirmation email has been sent to you.' => 'Gracias por participar, sus respuestas han sido registradas. Un correo electrónico se le ha enviado con su confirmación.',
			'Back To Assignments' => 'Regresar A Las Tareas',
			'Clear' => 'Borrar',
			'You recalled' => 'Recordaste',
			'out of' => 'de un total de',
			'letters correctly' => 'letras correctamente',
			'squares correctly' => 'cuadrados correctamente',
			'symmetry figure questions correctly' => 'figuras simétricas correctamente',
			'True' => 'Verdadero',
			'False' => 'Falso',
			'Correct' => 'Correcto',
			'correct' => 'correcto',
			'Incorrect' => 'Incorrecto',
			'incorrect' => 'incorrecto',
			'You answered' => 'Ha respondido',
			'Your answer is' => 'Su respuesta es',
			'math questions correctly' => 'preguntas de matemáticas correctamente',
			'Please try to solve each math problem correctly, as quickly as you can.' => 'Por favor, tratar de resolver cada problema matemático correctamente, tan pronto como sea posible.',
			'Please try to identify each symmetric and asymmetric figure correctly, as quickly as you can.' => 'Por favor, intente identificar cada figura simétrica y asimétrica correctamente, tan pronto como le sea posible.',
			'Got it' => 'Entendido',
			'All content associated with the test is copyrighted and may not be reproduced in any form.' => 'Todo el contenido asociado con la prueba tiene derechos de autor y no puede ser reproducido en cualquier forma.',
			'You may not record images of the test items in any form.' => 'El usuario no puede grabar imágenes de los elementos del ejercicio en cualquier forma.',
			'You may not use outside sources to help you answer the test items.' => 'El usuario no puede utilizar fuentes externas para ayudarle a responder a los elementos del ejercicio.',
			'You must verify that the person taking the test is the same person whose name is on the email that contained the test link.' => 'El usuario debe verificar que la persona que toma el ejercicio es la misma persona cuyo nombre aparece en el correo electrónico que contiene el enlace de prueba.',
			'Please recall the order of the blue boxes' => 'Por favor, recuerde el orden de los cuadrados azules',
		]
	];

	public function getTerms($language_code)
	{
		return $this->translated_terms[$language_code];
	}
}
