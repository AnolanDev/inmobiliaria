<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class SystemEmailTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Bienvenida a Nuevo Lead',
                'subject' => 'Bienvenido {{lead_first_name}} - {{company_name}}',
                'description' => 'Email de bienvenida enviado automáticamente a nuevos leads',
                'category' => 'welcome',
                'status' => 'active',
                'is_system_template' => true,
                'html_content' => $this->getWelcomeTemplate(),
                'text_content' => $this->getWelcomeTextTemplate(),
                'variables' => [
                    'lead_first_name', 'lead_full_name', 'company_name', 'company_website',
                    'assigned_agent_name', 'assigned_agent_email', 'assigned_agent_phone'
                ],
                'metadata' => [
                    'auto_send' => true,
                    'trigger' => 'lead_created',
                    'delay_minutes' => 5
                ]
            ],
            [
                'name' => 'Seguimiento Post-Visita',
                'subject' => 'Gracias por tu visita {{lead_first_name}} - ¿Cómo te pareció?',
                'description' => 'Email de seguimiento enviado después de una visita',
                'category' => 'follow_up',
                'status' => 'active',
                'is_system_template' => true,
                'html_content' => $this->getFollowUpTemplate(),
                'text_content' => $this->getFollowUpTextTemplate(),
                'variables' => [
                    'lead_first_name', 'lead_full_name', 'company_name',
                    'assigned_agent_name', 'property_address', 'visit_date'
                ],
                'metadata' => [
                    'auto_send' => true,
                    'trigger' => 'visit_completed',
                    'delay_hours' => 2
                ]
            ],
            [
                'name' => 'Newsletter Propiedades Nuevas',
                'subject' => 'Nuevas propiedades que podrían interesarte - {{company_name}}',
                'description' => 'Newsletter mensual con propiedades nuevas',
                'category' => 'newsletter',
                'status' => 'active',
                'is_system_template' => true,
                'html_content' => $this->getNewsletterTemplate(),
                'text_content' => $this->getNewsletterTextTemplate(),
                'variables' => [
                    'recipient_name', 'company_name', 'current_month', 'property_count'
                ],
                'metadata' => [
                    'frequency' => 'monthly',
                    'send_day' => 1,
                    'send_time' => '10:00'
                ]
            ],
            [
                'name' => 'Recordatorio de Cita',
                'subject' => 'Recordatorio: Tu cita mañana a las {{appointment_time}}',
                'description' => 'Recordatorio automático de citas programadas',
                'category' => 'appointment',
                'status' => 'active',
                'is_system_template' => true,
                'html_content' => $this->getAppointmentReminderTemplate(),
                'text_content' => $this->getAppointmentReminderTextTemplate(),
                'variables' => [
                    'lead_first_name', 'appointment_time', 'appointment_date',
                    'property_address', 'assigned_agent_name', 'assigned_agent_phone'
                ],
                'metadata' => [
                    'auto_send' => true,
                    'trigger' => 'appointment_scheduled',
                    'send_before_hours' => 24
                ]
            ],
            [
                'name' => 'Reactivación de Lead Inactivo',
                'subject' => '{{lead_first_name}}, ¿Sigues buscando? Tenemos novedades',
                'description' => 'Email para reactivar leads que han estado inactivos',
                'category' => 'reactivation',
                'status' => 'active',
                'is_system_template' => true,
                'html_content' => $this->getReactivationTemplate(),
                'text_content' => $this->getReactivationTextTemplate(),
                'variables' => [
                    'lead_first_name', 'company_name', 'last_interaction_date',
                    'new_properties_count', 'assigned_agent_name'
                ],
                'metadata' => [
                    'auto_send' => true,
                    'trigger' => 'lead_inactive',
                    'inactive_days' => 30
                ]
            ],
            [
                'name' => 'Felicitaciones por Compra',
                'subject' => '¡Felicitaciones {{lead_first_name}}! Bienvenido a tu nuevo hogar',
                'description' => 'Email de felicitación cuando un lead se convierte en cliente',
                'category' => 'congratulations',
                'status' => 'active',
                'is_system_template' => true,
                'html_content' => $this->getCongratulationsTemplate(),
                'text_content' => $this->getCongratulationsTextTemplate(),
                'variables' => [
                    'lead_first_name', 'lead_full_name', 'property_address',
                    'company_name', 'assigned_agent_name', 'purchase_date'
                ],
                'metadata' => [
                    'auto_send' => true,
                    'trigger' => 'lead_converted',
                    'delay_hours' => 1
                ]
            ]
        ];

        foreach ($templates as $templateData) {
            // Add system user as creator
            $templateData['created_by'] = 1; // Assuming user ID 1 is the system admin
            
            EmailTemplate::firstOrCreate(
                [
                    'name' => $templateData['name'],
                    'is_system_template' => true
                ],
                $templateData
            );
        }

        $this->command->info('System email templates created successfully.');
    }

    private function getWelcomeTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a {{company_name}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; margin-top: 20px; }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #3b82f6; }
        .logo { font-size: 24px; font-weight: bold; color: #3b82f6; }
        .content { padding: 30px 0; }
        .welcome { font-size: 28px; color: #3b82f6; margin-bottom: 20px; }
        .agent-info { background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px 0; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; }
        .btn { display: inline-block; background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{company_name}}</div>
        </div>
        
        <div class="content">
            <h1 class="welcome">¡Bienvenido {{lead_first_name}}!</h1>
            
            <p>Nos complace tenerte como parte de nuestra comunidad. En <strong>{{company_name}}</strong> estamos comprometidos a ayudarte a encontrar la propiedad perfecta que se adapte a tus necesidades y presupuesto.</p>
            
            <div class="agent-info">
                <h3>Tu agente asignado:</h3>
                <p><strong>{{assigned_agent_name}}</strong></p>
                <p>📧 {{assigned_agent_email}}</p>
                <p>📞 {{assigned_agent_phone}}</p>
            </div>
            
            <p>Tu agente se pondrá en contacto contigo pronto para conocer mejor tus preferencias y comenzar la búsqueda de tu propiedad ideal.</p>
            
            <p>Mientras tanto, te invitamos a explorar nuestro portafolio de propiedades disponibles:</p>
            
            <a href="{{company_website}}" class="btn">Ver Propiedades</a>
            
            <p>Si tienes alguna pregunta, no dudes en contactarnos. ¡Estamos aquí para ayudarte!</p>
        </div>
        
        <div class="footer">
            <p>{{company_name}} - Tu hogar ideal te está esperando</p>
            <p><a href="{{company_website}}">{{company_website}}</a></p>
        </div>
    </div>
</body>
</html>';
    }

    private function getWelcomeTextTemplate(): string
    {
        return 'Bienvenido {{lead_first_name}} a {{company_name}}

Nos complace tenerte como parte de nuestra comunidad. Estamos comprometidos a ayudarte a encontrar la propiedad perfecta.

Tu agente asignado:
{{assigned_agent_name}}
Email: {{assigned_agent_email}}
Teléfono: {{assigned_agent_phone}}

Tu agente se pondrá en contacto contigo pronto para conocer mejor tus preferencias.

Visita nuestro sitio web: {{company_website}}

¡Estamos aquí para ayudarte!

{{company_name}}';
    }

    private function getFollowUpTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de Visita</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; margin-top: 20px; }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #10b981; }
        .logo { font-size: 24px; font-weight: bold; color: #10b981; }
        .content { padding: 30px 0; }
        .title { font-size: 24px; color: #10b981; margin-bottom: 20px; }
        .feedback-section { background: #f0fdf4; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px 0; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; }
        .btn { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{company_name}}</div>
        </div>
        
        <div class="content">
            <h1 class="title">Gracias por tu visita, {{lead_first_name}}</h1>
            
            <p>Esperamos que hayas disfrutado tu visita a la propiedad el {{visit_date}}. Tu opinión es muy importante para nosotros.</p>
            
            <div class="feedback-section">
                <h3>¿Cómo fue tu experiencia?</h3>
                <p>Nos encantaría conocer tus impresiones sobre la propiedad y nuestro servicio:</p>
                
                <a href="#" class="btn">😊 Excelente</a>
                <a href="#" class="btn">👍 Buena</a>
                <a href="#" class="btn">👎 Regular</a>
            </div>
            
            <p>Si tienes alguna pregunta adicional sobre la propiedad o te gustaría programar otra visita, no dudes en contactar directamente a {{assigned_agent_name}}.</p>
            
            <p>También podemos ayudarte a encontrar otras propiedades que se ajusten mejor a tus necesidades.</p>
            
            <p><strong>Próximos pasos:</strong></p>
            <ul>
                <li>Revisaremos propiedades similares en tu zona de interés</li>
                <li>Te mantendremos informado sobre nuevas oportunidades</li>
                <li>Estamos disponibles para responder cualquier pregunta</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>{{company_name}} - Encontrando tu hogar perfecto</p>
            <p>{{assigned_agent_name}} - {{assigned_agent_email}}</p>
        </div>
    </div>
</body>
</html>';
    }

    private function getFollowUpTextTemplate(): string
    {
        return 'Gracias por tu visita, {{lead_first_name}}

Esperamos que hayas disfrutado tu visita a la propiedad el {{visit_date}}.

¿Cómo fue tu experiencia? Tu opinión es muy importante para nosotros.

Si tienes alguna pregunta adicional o te gustaría programar otra visita, contacta a {{assigned_agent_name}}.

Próximos pasos:
- Revisaremos propiedades similares
- Te mantendremos informado sobre nuevas oportunidades
- Estamos disponibles para cualquier pregunta

{{company_name}}
{{assigned_agent_name}} - {{assigned_agent_email}}';
    }

    private function getNewsletterTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter {{current_month}} - {{company_name}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: white; margin-top: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 20px; text-align: center; }
        .logo { font-size: 28px; font-weight: bold; margin-bottom: 10px; }
        .subtitle { font-size: 16px; opacity: 0.9; }
        .content { padding: 30px 20px; }
        .section { margin-bottom: 30px; }
        .properties { display: flex; flex-wrap: wrap; gap: 20px; }
        .property { border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .property img { width: 100%; height: 200px; object-fit: cover; }
        .property-info { padding: 15px; }
        .price { font-size: 20px; font-weight: bold; color: #10b981; }
        .stats { background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .footer { background: #374151; color: white; padding: 20px; text-align: center; }
        .btn { display: inline-block; background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{company_name}}</div>
            <div class="subtitle">Newsletter {{current_month}} - Nuevas Oportunidades</div>
        </div>
        
        <div class="content">
            <div class="section">
                <h2>Hola {{recipient_name}},</h2>
                <p>Este mes hemos agregado <strong>{{property_count}} nuevas propiedades</strong> a nuestro portafolio. Aquí tienes un resumen de las mejores oportunidades:</p>
            </div>
            
            <div class="section">
                <h3>🏠 Propiedades Destacadas</h3>
                
                <div class="property">
                    <div class="property-info">
                        <div class="price">$450,000</div>
                        <h4>Casa en Zona Residencial</h4>
                        <p>3 habitaciones, 2 baños, jardín privado. Excelente ubicación cerca de escuelas y centros comerciales.</p>
                        <a href="#" class="btn">Ver Detalles</a>
                    </div>
                </div>
                
                <div class="property">
                    <div class="property-info">
                        <div class="price">$320,000</div>
                        <h4>Apartamento Moderno</h4>
                        <p>2 habitaciones, 1 baño, balcón con vista. Edificio nuevo con amenidades completas.</p>
                        <a href="#" class="btn">Ver Detalles</a>
                    </div>
                </div>
            </div>
            
            <div class="stats">
                <h3>📊 Tendencias del Mercado</h3>
                <ul>
                    <li>Las propiedades se venden 15% más rápido este mes</li>
                    <li>Mayor demanda en zonas residenciales familiares</li>
                    <li>Apartamentos modernos con alta valorización</li>
                </ul>
            </div>
            
            <div class="section">
                <h3>💡 Consejos del Mes</h3>
                <p>Este es el momento ideal para invertir en propiedades. Las tasas de interés están favorables y hay excelentes oportunidades disponibles.</p>
            </div>
            
            <p>¿Te interesa alguna de estas propiedades? ¡Contáctanos para programar una visita!</p>
            
            <a href="{{company_website}}" class="btn">Ver Todas las Propiedades</a>
        </div>
        
        <div class="footer">
            <p><strong>{{company_name}}</strong></p>
            <p>Tu hogar ideal te está esperando</p>
            <p><a href="{{company_website}}" style="color: #60a5fa;">{{company_website}}</a></p>
        </div>
    </div>
</body>
</html>';
    }

    private function getNewsletterTextTemplate(): string
    {
        return 'Newsletter {{current_month}} - {{company_name}}

Hola {{recipient_name}},

Este mes hemos agregado {{property_count}} nuevas propiedades a nuestro portafolio.

PROPIEDADES DESTACADAS:

Casa en Zona Residencial - $450,000
3 habitaciones, 2 baños, jardín privado. Excelente ubicación.

Apartamento Moderno - $320,000  
2 habitaciones, 1 baño, balcón con vista. Edificio nuevo.

TENDENCIAS DEL MERCADO:
- Las propiedades se venden 15% más rápido
- Mayor demanda en zonas residenciales
- Apartamentos modernos con alta valorización

CONSEJO DEL MES:
Este es el momento ideal para invertir. Tasas favorables y excelentes oportunidades.

¿Te interesa alguna propiedad? ¡Contáctanos!

Ver todas: {{company_website}}

{{company_name}}';
    }

    private function getAppointmentReminderTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio de Cita</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; margin-top: 20px; }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #f59e0b; }
        .logo { font-size: 24px; font-weight: bold; color: #f59e0b; }
        .content { padding: 30px 0; }
        .reminder { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 20px; margin: 20px 0; }
        .appointment-details { background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px 0; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; }
        .btn { display: inline-block; background: #f59e0b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 5px; }
        .icon { font-size: 48px; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{company_name}}</div>
        </div>
        
        <div class="content">
            <div class="icon">⏰</div>
            
            <h1 style="text-align: center; color: #f59e0b;">Recordatorio de Cita</h1>
            
            <div class="reminder">
                <h3>¡No olvides tu cita mañana!</h3>
                <p>Hola {{lead_first_name}}, este es un recordatorio de tu cita programada para mañana.</p>
            </div>
            
            <div class="appointment-details">
                <h3>Detalles de la Cita:</h3>
                <p><strong>📅 Fecha:</strong> {{appointment_date}}</p>
                <p><strong>🕐 Hora:</strong> {{appointment_time}}</p>
                <p><strong>📍 Propiedad:</strong> {{property_address}}</p>
                <p><strong>👤 Agente:</strong> {{assigned_agent_name}}</p>
                <p><strong>📞 Teléfono:</strong> {{assigned_agent_phone}}</p>
            </div>
            
            <p><strong>Recomendaciones para la visita:</strong></p>
            <ul>
                <li>Llega 5 minutos antes de la hora programada</li>
                <li>Trae una identificación oficial</li>
                <li>Prepara tus preguntas sobre la propiedad</li>
                <li>Si necesitas cambiar la cita, avísanos con anticipación</li>
            </ul>
            
            <p>Si tienes alguna pregunta o necesitas reprogramar, no dudes en contactar a {{assigned_agent_name}}.</p>
            
            <div style="text-align: center;">
                <a href="tel:{{assigned_agent_phone}}" class="btn">📞 Llamar Agente</a>
                <a href="#" class="btn">📍 Ver Ubicación</a>
            </div>
            
            <p style="text-align: center; font-style: italic;">¡Estamos emocionados de mostrarte esta propiedad!</p>
        </div>
        
        <div class="footer">
            <p>{{company_name}} - Tu cita es importante para nosotros</p>
            <p>{{assigned_agent_name}} - {{assigned_agent_phone}}</p>
        </div>
    </div>
</body>
</html>';
    }

    private function getAppointmentReminderTextTemplate(): string
    {
        return 'RECORDATORIO DE CITA - {{company_name}}

¡No olvides tu cita mañana!

Hola {{lead_first_name}}, recordatorio de tu cita programada:

DETALLES:
📅 Fecha: {{appointment_date}}
🕐 Hora: {{appointment_time}}
📍 Propiedad: {{property_address}}
👤 Agente: {{assigned_agent_name}}
📞 Teléfono: {{assigned_agent_phone}}

RECOMENDACIONES:
- Llega 5 minutos antes
- Trae identificación oficial
- Prepara tus preguntas
- Avisa si necesitas reprogramar

¡Estamos emocionados de mostrarte esta propiedad!

{{company_name}}';
    }

    private function getReactivationTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Te extrañamos - {{company_name}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; margin-top: 20px; }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #8b5cf6; }
        .logo { font-size: 24px; font-weight: bold; color: #8b5cf6; }
        .content { padding: 30px 0; }
        .comeback { background: #faf5ff; border: 2px solid #8b5cf6; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
        .offers { background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px 0; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; }
        .btn { display: inline-block; background: #8b5cf6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 0; }
        .highlight { background: #fef3c7; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{company_name}}</div>
        </div>
        
        <div class="content">
            <h1 style="color: #8b5cf6; text-align: center;">¡Te extrañamos {{lead_first_name}}!</h1>
            
            <div class="comeback">
                <h2>🏠 ¿Sigues buscando tu hogar ideal?</h2>
                <p>Hemos notado que no has estado activo desde el {{last_interaction_date}}. ¡Tenemos excelentes noticias para ti!</p>
            </div>
            
            <p>Desde tu última visita, hemos agregado <span class="highlight">{{new_properties_count}} nuevas propiedades</span> que podrían ser perfectas para ti.</p>
            
            <div class="offers">
                <h3>🎯 Ofertas Especiales para Ti:</h3>
                <ul>
                    <li><strong>Asesoramiento gratuito</strong> personalizado</li>
                    <li><strong>Acceso prioritario</strong> a nuevas propiedades</li>
                    <li><strong>Visitas VIP</strong> con horarios flexibles</li>
                    <li><strong>Análisis de mercado</strong> sin costo</li>
                </ul>
            </div>
            
            <p><strong>¿Qué ha cambiado en tu búsqueda?</strong></p>
            <ul>
                <li>¿Cambió tu presupuesto?</li>
                <li>¿Tienes nuevas preferencias de ubicación?</li>
                <li>¿Necesitas un tipo diferente de propiedad?</li>
            </ul>
            
            <p>Nuestro agente {{assigned_agent_name}} está listo para ayudarte a encontrar exactamente lo que buscas.</p>
            
            <div style="text-align: center;">
                <a href="#" class="btn">🔍 Ver Nuevas Propiedades</a>
                <a href="#" class="btn">📞 Contactar Agente</a>
            </div>
            
            <p style="text-align: center; font-style: italic;">Tu hogar perfecto te está esperando. ¡No dejes que se te escape!</p>
        </div>
        
        <div class="footer">
            <p>{{company_name}} - Siempre aquí para ayudarte</p>
            <p>{{assigned_agent_name}} - Tu agente de confianza</p>
        </div>
    </div>
</body>
</html>';
    }

    private function getReactivationTextTemplate(): string
    {
        return '¡Te extrañamos {{lead_first_name}}! - {{company_name}}

¿Sigues buscando tu hogar ideal?

Hemos notado que no has estado activo desde el {{last_interaction_date}}.

¡Tenemos excelentes noticias! Desde tu última visita agregamos {{new_properties_count}} nuevas propiedades.

OFERTAS ESPECIALES PARA TI:
✓ Asesoramiento gratuito personalizado
✓ Acceso prioritario a nuevas propiedades  
✓ Visitas VIP con horarios flexibles
✓ Análisis de mercado sin costo

¿QUÉ HA CAMBIADO EN TU BÚSQUEDA?
- ¿Cambió tu presupuesto?
- ¿Nuevas preferencias de ubicación?
- ¿Diferente tipo de propiedad?

{{assigned_agent_name}} está listo para ayudarte.

Tu hogar perfecto te está esperando. ¡No dejes que se te escape!

{{company_name}}';
    }

    private function getCongratulationsTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Felicitaciones! - {{company_name}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; margin-top: 20px; }
        .header { text-align: center; padding: 20px 0; background: linear-gradient(135deg, #10b981, #059669); border-radius: 10px 10px 0 0; color: white; }
        .logo { font-size: 24px; font-weight: bold; margin-bottom: 10px; }
        .content { padding: 30px 0; }
        .celebration { text-align: center; font-size: 48px; margin: 20px 0; }
        .property-info { background: #ecfdf5; border: 2px solid #10b981; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .next-steps { background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px 0; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px; }
        .btn { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{company_name}}</div>
            <h1 style="margin: 0;">¡FELICITACIONES!</h1>
        </div>
        
        <div class="content">
            <div class="celebration">🎉🏠🎊</div>
            
            <h2 style="text-align: center; color: #10b981;">¡Bienvenido a tu nuevo hogar, {{lead_first_name}}!</h2>
            
            <p>Es un honor para {{company_name}} haber sido parte de este momento tan especial en tu vida. Sabemos que encontrar el hogar perfecto es una decisión importante, y estamos felices de que hayas confiado en nosotros.</p>
            
            <div class="property-info">
                <h3>🏠 Tu Nueva Propiedad:</h3>
                <p><strong>Dirección:</strong> {{property_address}}</p>
                <p><strong>Fecha de Compra:</strong> {{purchase_date}}</p>
                <p><strong>Agente Responsable:</strong> {{assigned_agent_name}}</p>
            </div>
            
            <p>Estamos seguros de que crearás hermosos recuerdos en tu nuevo hogar. Esta es una nueva etapa llena de posibilidades y momentos especiales.</p>
            
            <div class="next-steps">
                <h3>📋 Próximos Pasos:</h3>
                <ul>
                    <li>✅ Documentación legal procesada</li>
                    <li>🔑 Entrega de llaves coordinada</li>
                    <li>📋 Revisión final de la propiedad</li>
                    <li>🏠 ¡Disfruta tu nuevo hogar!</li>
                </ul>
            </div>
            
            <p><strong>Nuestro compromiso continúa:</strong></p>
            <ul>
                <li>Seguimiento post-venta personalizado</li>
                <li>Asesoría para futuras inversiones</li>
                <li>Red de contactos para servicios del hogar</li>
                <li>Invitaciones a eventos exclusivos para propietarios</li>
            </ul>
            
            <div style="text-align: center;">
                <a href="#" class="btn">🏠 Guía del Nuevo Propietario</a>
                <a href="#" class="btn">📞 Contactar Agente</a>
            </div>
            
            <p style="text-align: center; font-weight: bold; color: #10b981;">¡Que disfrutes muchos años de felicidad en tu nuevo hogar!</p>
        </div>
        
        <div class="footer">
            <p><strong>{{company_name}}</strong> - Orgullosos de ser parte de tu historia</p>
            <p>{{assigned_agent_name}} - Siempre a tu servicio</p>
        </div>
    </div>
</body>
</html>';
    }

    private function getCongratulationsTextTemplate(): string
    {
        return '¡FELICITACIONES {{lead_first_name}}! - {{company_name}}

🎉 ¡Bienvenido a tu nuevo hogar! 🏠

Es un honor haber sido parte de este momento especial. Sabemos que encontrar el hogar perfecto es importante, y estamos felices de tu confianza.

TU NUEVA PROPIEDAD:
🏠 Dirección: {{property_address}}
📅 Fecha: {{purchase_date}}  
👤 Agente: {{assigned_agent_name}}

Estamos seguros de que crearás hermosos recuerdos en tu nuevo hogar.

PRÓXIMOS PASOS:
✅ Documentación legal procesada
🔑 Entrega de llaves coordinada  
📋 Revisión final de la propiedad
🏠 ¡Disfruta tu nuevo hogar!

NUESTRO COMPROMISO CONTINÚA:
- Seguimiento post-venta personalizado
- Asesoría para futuras inversiones
- Red de contactos para servicios del hogar
- Eventos exclusivos para propietarios

¡Que disfrutes muchos años de felicidad en tu nuevo hogar!

{{company_name}} - Orgullosos de ser parte de tu historia
{{assigned_agent_name}} - Siempre a tu servicio';
    }
}