<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Database\Seeder;

class DiagnosticSeeder extends Seeder
{
    public function run(): void
    {
        if (Category::exists()) {
            return;
        }

        // Bloque 1: Política de datos personales (Máximo 40%)
        $cat1 = Category::create([
            'name' => 'Política de datos personales',
            'description' => 'Evalúa si la organización cuenta con una política de tratamiento de datos personales alineada con la Ley 1581.',
            'max_percentage' => 40,
            'sort_order' => 1,
        ]);

        $q1 = Question::create([
            'category_id' => $cat1->id,
            'question_text' => '¿Cuenta con una política de tratamiento de datos personales?',
            'help_text' => 'Una política de tratamiento de datos personales es un documento formal que establece cómo la organización recolecta, usa, almacena y protege los datos personales.',
            'weight' => 0,
            'sort_order' => 1,
            'is_complementary' => false,
        ]);

        Question::create([
            'category_id' => $cat1->id,
            'parent_question_id' => $q1->id,
            'question_text' => '¿La política está documentada y publicada en un medio de fácil acceso?',
            'help_text' => 'La política debe estar disponible para todos los interesados (sitio web, intranet, etc.) y redactada en lenguaje claro.',
            'weight' => 10,
            'sort_order' => 2,
            'is_complementary' => false,
        ]);

        Question::create([
            'category_id' => $cat1->id,
            'parent_question_id' => $q1->id,
            'question_text' => '¿Define las finalidades del tratamiento de datos?',
            'help_text' => 'La política debe especificar para qué fines se recolectan y tratan los datos personales (ej. comerciales, administrativos, de servicio).',
            'weight' => 10,
            'sort_order' => 3,
            'is_complementary' => false,
        ]);

        Question::create([
            'category_id' => $cat1->id,
            'parent_question_id' => $q1->id,
            'question_text' => '¿Incluye los derechos de los titulares?',
            'help_text' => 'Debe mencionar los derechos de acceso, rectificación, cancelación y oposición (derechos ARCO) que tienen los titulares sobre sus datos.',
            'weight' => 10,
            'sort_order' => 4,
            'is_complementary' => false,
        ]);

        Question::create([
            'category_id' => $cat1->id,
            'parent_question_id' => $q1->id,
            'question_text' => '¿Menciona cómo ejercer los derechos de los titulares?',
            'help_text' => 'La política debe explicar el procedimiento para que los titulares puedan ejercer sus derechos: canales de contacto, plazos, formatos.',
            'weight' => 10,
            'sort_order' => 5,
            'is_complementary' => false,
        ]);

        // Bloque 2: Privacidad desde el diseño (Máximo 36%)
        $cat2 = Category::create([
            'name' => 'Privacidad desde el diseño',
            'description' => 'Evalúa si la organización incorpora principios de privacidad en la fase de diseño de sus sistemas y procesos.',
            'max_percentage' => 36,
            'sort_order' => 2,
        ]);

        Question::create([
            'category_id' => $cat2->id,
            'question_text' => '¿Incorpora evaluaciones de impacto (Privacy Impact Assessments)?',
            'help_text' => 'Las PIA son análisis sistemáticos para identificar y mitigar riesgos de privacidad en nuevos proyectos o procesos que involucren datos personales.',
            'weight' => 12,
            'sort_order' => 1,
            'is_complementary' => false,
        ]);

        Question::create([
            'category_id' => $cat2->id,
            'question_text' => '¿Aplica técnicas de minimización de datos?',
            'help_text' => 'La minimización de datos consiste en recolectar solo los datos estrictamente necesarios para la finalidad declarada.',
            'weight' => 12,
            'sort_order' => 2,
            'is_complementary' => false,
        ]);

        Question::create([
            'category_id' => $cat2->id,
            'question_text' => '¿Configura sus sistemas para recopilar el mínimo de datos por defecto?',
            'help_text' => 'Los sistemas deben estar configurados para recolectar la menor cantidad de datos posible por defecto (privacy by default).',
            'weight' => 12,
            'sort_order' => 3,
            'is_complementary' => false,
        ]);

        // Bloque 3: Gobernanza (Máximo 24%)
        $cat3 = Category::create([
            'name' => 'Gobernanza',
            'description' => 'Evalúa la estructura de gobierno y gestión de riesgos en materia de protección de datos.',
            'max_percentage' => 24,
            'sort_order' => 3,
        ]);

        Question::create([
            'category_id' => $cat3->id,
            'question_text' => '¿Cuenta con un sistema de administración de riesgos?',
            'help_text' => 'Un sistema de administración de riesgos permite identificar, evaluar y mitigar los riesgos asociados al tratamiento de datos personales.',
            'weight' => 16,
            'sort_order' => 1,
            'is_complementary' => false,
        ]);

        $q10 = Question::create([
            'category_id' => $cat3->id,
            'question_text' => '¿Cuenta con un oficial de protección de datos personales?',
            'help_text' => 'El oficial de protección de datos (DPO) es la persona encargada de velar por el cumplimiento de la normativa de protección de datos en la organización.',
            'weight' => 8,
            'sort_order' => 2,
            'is_complementary' => false,
        ]);

        Question::create([
            'category_id' => $cat3->id,
            'parent_question_id' => $q10->id,
            'question_text' => '¿Está designado formalmente?',
            'help_text' => 'El oficial debe tener un nombramiento formal y estar debidamente registrado ante la autoridad de protección de datos (SNR).',
            'weight' => 0,
            'sort_order' => 3,
            'is_complementary' => true,
        ]);
    }
}
