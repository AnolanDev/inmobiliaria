<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogs = [
            [
                'title' => 'Tendencias del mercado inmobiliario 2025',
                'excerpt' => 'Descubre las principales tendencias que marcarán el sector inmobiliario este año, desde la sostenibilidad hasta la digitalización.',
                'content' => '<h2>El futuro del sector inmobiliario</h2>
                <p>El mercado inmobiliario está experimentando una transformación sin precedentes. Las nuevas tecnologías, el cambio climático y las preferencias de los consumidores están redefiniendo la industria.</p>
                
                <h3>Tendencias principales para 2025</h3>
                <ul>
                    <li><strong>Sostenibilidad:</strong> Los edificios ecológicos y eficientes energéticamente son cada vez más demandados.</li>
                    <li><strong>Tecnología:</strong> Realidad virtual, inteligencia artificial y automatización del hogar.</li>
                    <li><strong>Espacios flexibles:</strong> Viviendas adaptables para trabajo remoto y múltiples usos.</li>
                    <li><strong>Ubicaciones emergentes:</strong> Crecimiento en zonas suburbanas y ciudades medianas.</li>
                </ul>
                
                <p>En Tierra Soñada, estamos preparados para ayudarte a navegar estas tendencias y encontrar la propiedad perfecta para el futuro.</p>',
                'author' => 'María González, Analista Inmobiliario',
                'category' => 'mercado',
                'tags' => ['tendencias', 'mercado', '2025', 'sostenibilidad'],
                'status' => 'published',
                'is_public' => true,
                'sort_order' => 1,
                'published_at' => now()->subDays(2),
                'views_count' => 145,
                'meta_title' => 'Tendencias del mercado inmobiliario 2025 | Tierra Soñada',
                'meta_description' => 'Conoce las principales tendencias del mercado inmobiliario para 2025. Sostenibilidad, tecnología y nuevas preferencias de compra.',
                'meta_keywords' => ['mercado inmobiliario', 'tendencias 2025', 'sostenibilidad', 'propiedades'],
            ],
            [
                'title' => 'Guía completa para comprar tu primera vivienda',
                'excerpt' => 'Todo lo que necesitas saber para dar el paso más importante: comprar tu primera casa. Desde la financiación hasta la escrituración.',
                'content' => '<h2>Tu primera vivienda: el sueño hecho realidad</h2>
                <p>Comprar tu primera vivienda es uno de los momentos más emocionantes y, a la vez, más desafiantes de la vida. Esta guía te acompañará en cada paso del proceso.</p>
                
                <h3>Paso 1: Evalúa tu capacidad financiera</h3>
                <p>Antes de empezar a buscar, es fundamental conocer cuánto puedes permitirte gastar. Considera:</p>
                <ul>
                    <li>Tus ingresos mensuales estables</li>
                    <li>Gastos fijos y variables</li>
                    <li>Ahorros disponibles para la entrada</li>
                    <li>Capacidad de endeudamiento</li>
                </ul>
                
                <h3>Paso 2: Obtén pre-aprobación hipotecaria</h3>
                <p>La pre-aprobación te dará una idea clara de tu presupuesto y te hará más atractivo para los vendedores.</p>
                
                <h3>Paso 3: Define tus necesidades</h3>
                <ul>
                    <li>Ubicación preferida</li>
                    <li>Tipo de vivienda</li>
                    <li>Número de habitaciones</li>
                    <li>Servicios y amenidades</li>
                </ul>
                
                <p>En Tierra Soñada, nuestros expertos te acompañarán en todo el proceso, desde la búsqueda hasta la entrega de llaves.</p>',
                'author' => 'Carlos Ruiz, Asesor Inmobiliario Senior',
                'category' => 'consejos',
                'tags' => ['primera vivienda', 'consejos', 'compra', 'hipoteca'],
                'status' => 'published',
                'is_public' => true,
                'sort_order' => 2,
                'published_at' => now()->subDays(5),
                'views_count' => 298,
                'meta_title' => 'Guía para comprar tu primera vivienda | Tierra Soñada',
                'meta_description' => 'Guía completa paso a paso para comprar tu primera vivienda. Financiación, búsqueda y consejos de expertos.',
                'meta_keywords' => ['primera vivienda', 'comprar casa', 'hipoteca', 'consejos inmobiliarios'],
            ],
            [
                'title' => 'Inversión inmobiliaria: estrategias para maximizar rentabilidad',
                'excerpt' => 'Aprende las mejores estrategias de inversión inmobiliaria para generar ingresos pasivos y construir patrimonio a largo plazo.',
                'content' => '<h2>Inversión inmobiliaria inteligente</h2>
                <p>La inversión inmobiliaria sigue siendo una de las formas más sólidas de construir riqueza a largo plazo. Te compartimos estrategias probadas para maximizar tu rentabilidad.</p>
                
                <h3>Tipos de inversión inmobiliaria</h3>
                <h4>1. Buy and Hold (Comprar y mantener)</h4>
                <p>Estrategia a largo plazo que busca generar ingresos por alquiler y apreciación del capital.</p>
                
                <h4>2. Fix and Flip (Renovar y vender)</h4>
                <p>Compra de propiedades para renovar y vender rápidamente con ganancia.</p>
                
                <h4>3. Propiedades de alquiler vacacional</h4>
                <p>Inversión en propiedades para alquiler de corta duración (Airbnb, etc.).</p>
                
                <h3>Factores clave para el éxito</h3>
                <ul>
                    <li><strong>Ubicación:</strong> El factor más importante en bienes raíces</li>
                    <li><strong>Análisis de mercado:</strong> Comprende la oferta y demanda local</li>
                    <li><strong>Flujo de caja:</strong> Asegúrate de que los ingresos superen los gastos</li>
                    <li><strong>Diversificación:</strong> No pongas todos los huevos en una canasta</li>
                </ul>
                
                <h3>Métricas importantes</h3>
                <ul>
                    <li><strong>ROI (Return on Investment):</strong> Rentabilidad sobre la inversión</li>
                    <li><strong>Cap Rate:</strong> Tasa de capitalización</li>
                    <li><strong>Cash-on-Cash Return:</strong> Retorno sobre el efectivo invertido</li>
                </ul>
                
                <p>Nuestro equipo de asesores especializados en inversión puede ayudarte a identificar las mejores oportunidades del mercado.</p>',
                'author' => 'Ana Patricia López, Especialista en Inversiones',
                'category' => 'inversion',
                'tags' => ['inversión', 'rentabilidad', 'ROI', 'estrategias'],
                'status' => 'published',
                'is_public' => true,
                'sort_order' => 3,
                'published_at' => now()->subDays(8),
                'views_count' => 187,
                'meta_title' => 'Estrategias de inversión inmobiliaria | Tierra Soñada',
                'meta_description' => 'Descubre las mejores estrategias de inversión inmobiliaria para maximizar rentabilidad y construir patrimonio.',
                'meta_keywords' => ['inversión inmobiliaria', 'rentabilidad', 'estrategias', 'ROI'],
            ],
            [
                'title' => 'Aspectos legales esenciales en la compra de propiedades',
                'excerpt' => 'Conoce los aspectos legales fundamentales que debes considerar al comprar una propiedad para evitar problemas futuros.',
                'content' => '<h2>Protege tu inversión: aspectos legales clave</h2>
                <p>La compra de una propiedad involucra múltiples aspectos legales que debes conocer para proteger tu inversión y evitar problemas futuros.</p>
                
                <h3>Documentos esenciales</h3>
                <h4>1. Escritura de propiedad</h4>
                <p>Documento que acredita la propiedad legal del inmueble. Verifica que esté registrada y libre de gravámenes.</p>
                
                <h4>2. Certificado de libertad y tradición</h4>
                <p>Muestra el historial legal de la propiedad, incluyendo propietarios anteriores, hipotecas y embargos.</p>
                
                <h4>3. Licencias y permisos</h4>
                <p>Verifica que la construcción tenga todas las licencias de construcción y habitabilidad vigentes.</p>
                
                <h3>Verificaciones importantes</h3>
                <ul>
                    <li><strong>Estado de servicios públicos:</strong> Agua, luz, gas, teléfono</li>
                    <li><strong>Impuestos prediales:</strong> Asegúrate de que estén al día</li>
                    <li><strong>Administración:</strong> En caso de conjuntos, verifica el estado de cuotas</li>
                    <li><strong>Zonificación:</strong> Confirma el uso permitido del suelo</li>
                </ul>
                
                <h3>El proceso de escrituración</h3>
                <ol>
                    <li>Firma de promesa de compraventa</li>
                    <li>Pago del anticipo (generalmente 10-30%)</li>
                    <li>Verificación de documentos</li>
                    <li>Trámite del crédito hipotecario (si aplica)</li>
                    <li>Firma de escritura pública ante notario</li>
                    <li>Registro en la Oficina de Instrumentos Públicos</li>
                </ol>
                
                <h3>Consejos de seguridad</h3>
                <ul>
                    <li>Siempre trabaja con profesionales certificados</li>
                    <li>Lee todos los documentos antes de firmar</li>
                    <li>Verifica la identidad del vendedor</li>
                    <li>Considera contratar un abogado inmobiliario</li>
                </ul>
                
                <p>En Tierra Soñada, trabajamos con abogados especializados para garantizar que todos los aspectos legales de tu compra estén perfectamente en orden.</p>',
                'author' => 'Dr. Roberto Mendoza, Abogado Inmobiliario',
                'category' => 'legal',
                'tags' => ['aspectos legales', 'escritura', 'documentos', 'compra segura'],
                'status' => 'published',
                'is_public' => true,
                'sort_order' => 4,
                'published_at' => now()->subDays(12),
                'views_count' => 156,
                'meta_title' => 'Aspectos legales en compra de propiedades | Tierra Soñada',
                'meta_description' => 'Guía completa sobre aspectos legales esenciales en la compra de propiedades. Documentos, verificaciones y proceso.',
                'meta_keywords' => ['aspectos legales', 'compra propiedad', 'escritura', 'documentos legales'],
            ],
            [
                'title' => 'Opciones de financiación para tu nueva vivienda',
                'excerpt' => 'Explora las diferentes opciones de financiación disponibles para hacer realidad el sueño de tu nueva vivienda.',
                'content' => '<h2>Financiación inteligente para tu hogar</h2>
                <p>Existen múltiples opciones de financiación que pueden ayudarte a adquirir tu nueva vivienda. Conoce cuál es la mejor para tu situación.</p>
                
                <h3>Tipos de crédito hipotecario</h3>
                
                <h4>1. Crédito hipotecario tradicional</h4>
                <ul>
                    <li><strong>Tasa fija:</strong> La cuota permanece constante durante todo el plazo</li>
                    <li><strong>Tasa variable:</strong> La cuota puede cambiar según las condiciones del mercado</li>
                    <li><strong>Tasa mixta:</strong> Combina períodos de tasa fija y variable</li>
                </ul>
                
                <h4>2. Crédito VIS (Vivienda de Interés Social)</h4>
                <p>Para viviendas de hasta 135 SMMLV, con subsidios del gobierno y tasas preferenciales.</p>
                
                <h4>3. Crédito VIP (Vivienda de Interés Prioritario)</h4>
                <p>Para viviendas de hasta 70 SMMLV, con mayores beneficios y subsidios.</p>
                
                <h3>Requisitos generales</h3>
                <ul>
                    <li>Ingresos demostrables y estables</li>
                    <li>Buen historial crediticio</li>
                    <li>Capacidad de pago (máximo 30% de ingresos)</li>
                    <li>Cuota inicial (generalmente 20-30%)</li>
                    <li>Avalúo de la propiedad</li>
                </ul>
                
                <h3>Subsidios disponibles</h3>
                <h4>Mi Casa Ya</h4>
                <p>Subsidio de hasta 30 SMMLV para vivienda nueva, dependiendo de los ingresos familiares.</p>
                
                <h4>Semillero de Propietarios</h4>
                <p>Para familias que nunca han tenido vivienda propia, con subsidios adicionales.</p>
                
                <h3>Consejos para obtener mejor financiación</h3>
                <ul>
                    <li>Compara ofertas de diferentes entidades</li>
                    <li>Mejora tu puntaje crediticio antes de aplicar</li>
                    <li>Ahorra la mayor cuota inicial posible</li>
                    <li>Considera los seguros y gastos adicionales</li>
                    <li>Negocia la tasa de interés</li>
                </ul>
                
                <h3>Alternativas de financiación</h3>
                <ul>
                    <li><strong>Leasing habitacional:</strong> Opción de compra al final del contrato</li>
                    <li><strong>Financiación directa con constructor:</strong> Planes de pago flexibles</li>
                    <li><strong>Cooperativas de vivienda:</strong> Soluciones colectivas</li>
                </ul>
                
                <p>Nuestros asesores financieros te ayudarán a encontrar la opción de financiación que mejor se adapte a tu perfil y necesidades.</p>',
                'author' => 'Lucía Herrera, Asesora Financiera',
                'category' => 'financiacion',
                'tags' => ['financiación', 'crédito hipotecario', 'subsidios', 'Mi Casa Ya'],
                'status' => 'published',
                'is_public' => true,
                'sort_order' => 5,
                'published_at' => now()->subDays(15),
                'views_count' => 223,
                'meta_title' => 'Opciones de financiación para vivienda | Tierra Soñada',
                'meta_description' => 'Conoce todas las opciones de financiación disponibles para comprar tu vivienda. Créditos, subsidios y consejos.',
                'meta_keywords' => ['financiación vivienda', 'crédito hipotecario', 'subsidios', 'Mi Casa Ya'],
            ],
            [
                'title' => 'El boom del sector inmobiliario en ciudades intermedias',
                'excerpt' => 'Las ciudades intermedias están experimentando un crecimiento inmobiliario sin precedentes. Descubre las oportunidades.',
                'content' => '<h2>Ciudades intermedias: el nuevo eldorado inmobiliario</h2>
                <p>Mientras las grandes ciudades enfrentan saturación y altos precios, las ciudades intermedias emergen como nuevos polos de desarrollo inmobiliario.</p>
                
                <h3>¿Por qué ciudades intermedias?</h3>
                <ul>
                    <li><strong>Precios más accesibles:</strong> Menor costo por metro cuadrado</li>
                    <li><strong>Mejor calidad de vida:</strong> Menos tráfico, contaminación y estrés</li>
                    <li><strong>Crecimiento económico:</strong> Nuevas empresas y oportunidades laborales</li>
                    <li><strong>Infraestructura en desarrollo:</strong> Inversión en vías, servicios y conectividad</li>
                </ul>
                
                <h3>Ciudades con mayor proyección</h3>
                <h4>Región Caribe</h4>
                <ul>
                    <li>Sincelejo - Crecimiento del sector agroindustrial</li>
                    <li>Montería - Desarrollo portuario y logístico</li>
                    <li>Valledupar - Expansión minero-energética</li>
                </ul>
                
                <h4>Región Andina</h4>
                <ul>
                    <li>Tunja - Polo educativo y tecnológico</li>
                    <li>Popayán - Centro histórico y turístico</li>
                    <li>Pasto - Corredor fronterizo comercial</li>
                </ul>
                
                <h4>Región Pacífica</h4>
                <ul>
                    <li>Palmira - Zona franca y agroindustria</li>
                    <li>Tumaco - Puerto y pesca industrial</li>
                </ul>
                
                <h3>Sectores más atractivos</h3>
                <ul>
                    <li><strong>Vivienda familiar:</strong> Casas con jardín y espacios amplios</li>
                    <li><strong>Apartamentos nuevos:</strong> Proyectos modernos con amenidades</li>
                    <li><strong>Locales comerciales:</strong> Centros comerciales y zonas empresariales</li>
                    <li><strong>Lotes urbanizables:</strong> Terrenos para desarrollo futuro</li>
                </ul>
                
                <h3>Ventajas para inversionistas</h3>
                <ul>
                    <li>Mayor rentabilidad por alquiler</li>
                    <li>Potencial de valorización a mediano plazo</li>
                    <li>Menor competencia</li>
                    <li>Facilidad de gestión y administración</li>
                </ul>
                
                <h3>Consideraciones importantes</h3>
                <ul>
                    <li>Investigar el plan de desarrollo municipal</li>
                    <li>Analizar la conectividad y transporte</li>
                    <li>Evaluar el crecimiento poblacional</li>
                    <li>Verificar la llegada de nuevas empresas</li>
                </ul>
                
                <p>En Tierra Soñada, tenemos presencia en las principales ciudades intermedias del país. Nuestros especialistas locales conocen las mejores oportunidades de cada mercado.</p>',
                'author' => 'Diego Morales, Analista de Mercados Regionales',
                'category' => 'noticias',
                'tags' => ['ciudades intermedias', 'oportunidades', 'crecimiento', 'inversión'],
                'status' => 'published',
                'is_public' => true,
                'sort_order' => 6,
                'published_at' => now()->subDays(18),
                'views_count' => 94,
                'meta_title' => 'Boom inmobiliario en ciudades intermedias | Tierra Soñada',
                'meta_description' => 'Descubre las oportunidades del boom inmobiliario en ciudades intermedias. Menor costo, mayor rentabilidad.',
                'meta_keywords' => ['ciudades intermedias', 'boom inmobiliario', 'oportunidades', 'inversión'],
            ],
            [
                'title' => 'Tecnología en el sector inmobiliario: PropTech Revolution',
                'excerpt' => 'La tecnología está revolucionando el sector inmobiliario. Conoce las innovaciones que están cambiando la forma de comprar, vender y gestionar propiedades.',
                'content' => '<h2>PropTech: La revolución digital inmobiliaria</h2>
                <p>La tecnología está transformando radicalmente el sector inmobiliario, desde la búsqueda de propiedades hasta la gestión de portafolios de inversión.</p>
                
                <h3>Principales innovaciones PropTech</h3>
                
                <h4>1. Realidad Virtual y Aumentada</h4>
                <ul>
                    <li><strong>Tours virtuales 360°:</strong> Recorre propiedades desde casa</li>
                    <li><strong>Realidad aumentada:</strong> Visualiza renovaciones y decoración</li>
                    <li><strong>Planos interactivos:</strong> Explora distribuciones en 3D</li>
                </ul>
                
                <h4>2. Inteligencia Artificial</h4>
                <ul>
                    <li><strong>Valuación automática:</strong> Estimaciones precisas de precios</li>
                    <li><strong>Matching inteligente:</strong> Conecta compradores con propiedades ideales</li>
                    <li><strong>Chatbots:</strong> Atención 24/7 para consultas</li>
                </ul>
                
                <h4>3. Blockchain y Tokenización</h4>
                <ul>
                    <li><strong>Contratos inteligentes:</strong> Automatización de transacciones</li>
                    <li><strong>Tokenización:</strong> Inversión fraccionada en propiedades</li>
                    <li><strong>Registro inmutable:</strong> Historial transparente de propiedades</li>
                </ul>
                
                <h3>Plataformas digitales emergentes</h3>
                <h4>Mercados en línea</h4>
                <p>Plataformas que conectan directamente compradores y vendedores, reduciendo intermediarios y costos.</p>
                
                <h4>Gestión de propiedades</h4>
                <p>Software para administrar portafolios, automatizar pagos y gestionar mantenimiento.</p>
                
                <h4>Financiación alternativa</h4>
                <p>Crowdfunding inmobiliario, préstamos P2P y nuevas formas de financiación.</p>
                
                <h3>IoT y Smart Homes</h3>
                <ul>
                    <li><strong>Domótica avanzada:</strong> Control total del hogar desde el móvil</li>
                    <li><strong>Sensores inteligentes:</strong> Monitoreo de consumo y seguridad</li>
                    <li><strong>Asistentes virtuales:</strong> Integración con Alexa, Google Home</li>
                    <li><strong>Eficiencia energética:</strong> Optimización automática de recursos</li>
                </ul>
                
                <h3>Big Data y Analytics</h3>
                <ul>
                    <li>Análisis predictivo de mercados</li>
                    <li>Identificación de tendencias de precios</li>
                    <li>Segmentación avanzada de clientes</li>
                    <li>Optimización de estrategias de marketing</li>
                </ul>
                
                <h3>Beneficios para compradores</h3>
                <ul>
                    <li>Proceso de búsqueda más eficiente</li>
                    <li>Mayor transparencia en precios</li>
                    <li>Acceso a más información</li>
                    <li>Transacciones más rápidas y seguras</li>
                </ul>
                
                <h3>El futuro cercano</h3>
                <ul>
                    <li><strong>Drones:</strong> Inspecciones y fotografía aérea automatizada</li>
                    <li><strong>5G:</strong> Conectividad ultra-rápida para aplicaciones inmobiliarias</li>
                    <li><strong>Gemelos digitales:</strong> Réplicas virtuales de propiedades</li>
                    <li><strong>Metaverso:</strong> Experiencias inmersivas de propiedades virtuales</li>
                </ul>
                
                <p>En Tierra Soñada, estamos a la vanguardia de la adopción tecnológica, implementando las últimas innovaciones para brindarte la mejor experiencia en tu búsqueda inmobiliaria.</p>',
                'author' => 'Ing. Sandra Jiménez, Directora de Innovación',
                'category' => 'inmobiliario',
                'tags' => ['PropTech', 'tecnología', 'innovación', 'realidad virtual'],
                'status' => 'draft',
                'is_public' => false,
                'sort_order' => 7,
                'published_at' => null,
                'views_count' => 0,
                'meta_title' => 'PropTech Revolution en el sector inmobiliario | Tierra Soñada',
                'meta_description' => 'Descubre cómo la tecnología está revolucionando el sector inmobiliario. PropTech, IA, blockchain y más.',
                'meta_keywords' => ['PropTech', 'tecnología inmobiliaria', 'innovación', 'realidad virtual'],
            ]
        ];

        foreach ($blogs as $blogData) {
            Blog::create($blogData);
        }
    }
}