<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $firstUser = User::first();
        $createdBy = $firstUser ? $firstUser->id : 1;

        $templates = [
            [
                'name' => 'Bienvenida Nuevo Lead',
                'subject' => '¡Bienvenido {{lead_first_name}}! Te ayudamos a encontrar tu hogar ideal',
                'description' => 'Template de bienvenida para nuevos leads que se registran en el sistema',
                'category' => 'welcome',
                'status' => 'active',
                'is_system_template' => true,
                'html_content' => '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a {{company_name}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; background: #f8fafc; }
        .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
        .button { display: inline-block; background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Bienvenido a {{company_name}}!</h1>
        </div>
        <div class="content">
            <h2>Hola {{lead_first_name}},</h2>
            <p>¡Gracias por confiar en nosotros para encontrar tu hogar ideal!</p>
            <p>Nos complace tenerte como parte de nuestra comunidad. Nuestro equipo de expertos está aquí para ayudarte en cada paso del proceso.</p>
            
            <h3>¿Qué sigue ahora?</h3>
            <ul>
                <li>Revisaremos tu perfil y preferencias</li>
                <li>Te contactaremos en las próximas 24 horas</li>
                <li>Te enviaremos propiedades que coincidan con tus intereses</li>
            </ul>
            
            <p>Tu agente asignado: <strong>{{assigned_agent_name}}</strong></p>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="#" class="button">Ver Propiedades Disponibles</a>
            </p>
            
            <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
            <p>¡Estamos emocionados de ayudarte a encontrar tu nuevo hogar!</p>
        </div>
        <div class="footer">
            <p>{{company_name}} - {{current_date}}</p>
            <p><a href="{{unsubscribe_url}}">Darse de baja</a></p>
        </div>
    </div>
</body>
</html>',
                'text_content' => 'Hola {{lead_first_name}},

¡Bienvenido a {{company_name}}!

Gracias por confiar en nosotros para encontrar tu hogar ideal.

¿Qué sigue ahora?
- Revisaremos tu perfil y preferencias
- Te contactaremos en las próximas 24 horas
- Te enviaremos propiedades que coincidan con tus intereses

Tu agente asignado: {{assigned_agent_name}}

Si tienes alguna pregunta, no dudes en contactarnos.

{{company_name}} - {{current_date}}
Darse de baja: {{unsubscribe_url}}',
                'created_by' => $createdBy
            ],
            [
                'name' => 'Seguimiento Post-Visita',
                'subject' => 'Gracias por visitar la propiedad {{lead_first_name}} - ¿Qué te pareció?',
                'description' => 'Template para enviar después de que un lead visite una propiedad',
                'category' => 'follow_up',
                'status' => 'active',
                'is_system_template' => true,
                'html_content' => '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de Visita</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #059669; color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; background: #f8fafc; }
        .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
        .button { display: inline-block; background: #059669; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; }
        .highlight { background: #ecfdf5; padding: 15px; border-left: 4px solid #059669; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¿Qué te pareció la visita?</h1>
        </div>
        <div class="content">
            <h2>Hola {{lead_first_name}},</h2>
            <p>Esperamos que hayas disfrutado tu visita de ayer. Nos encantaría conocer tu opinión sobre la propiedad.</p>
            
            <div class="highlight">
                <h3>¿Te interesa esta propiedad?</h3>
                <p>Si tienes preguntas adicionales o te gustaría programar otra visita, estamos aquí para ayudarte.</p>
            </div>
            
            <p><strong>Tu agente {{assigned_agent_name}} está disponible para:</strong></p>
            <ul>
                <li>Responder cualquier pregunta sobre la propiedad</li>
                <li>Programar visitas adicionales</li>
                <li>Mostrar propiedades similares</li>
                <li>Ayudarte con el proceso de compra</li>
            </ul>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="#" class="button">Contactar Agente</a>
                <a href="#" class="button" style="background: #6366f1; margin-left: 10px;">Ver Más Propiedades</a>
            </p>
            
            <p>¡Gracias por tu tiempo y esperamos poder ayudarte pronto!</p>
        </div>
        <div class="footer">
            <p>{{company_name}} - {{current_date}}</p>
            <p><a href="{{unsubscribe_url}}">Darse de baja</a></p>
        </div>
    </div>
</body>
</html>',
                'text_content' => 'Hola {{lead_first_name}},

¿Qué te pareció la visita?

Esperamos que hayas disfrutado tu visita de ayer. Nos encantaría conocer tu opinión sobre la propiedad.

Tu agente {{assigned_agent_name}} está disponible para:
- Responder cualquier pregunta sobre la propiedad
- Programar visitas adicionales  
- Mostrar propiedades similares
- Ayudarte con el proceso de compra

¡Gracias por tu tiempo y esperamos poder ayudarte pronto!

{{company_name}} - {{current_date}}
Darse de baja: {{unsubscribe_url}}',
                'created_by' => $createdBy
            ],
            [
                'name' => 'Newsletter Mensual',
                'subject' => 'Newsletter {{company_name}} - Nuevas Propiedades y Tendencias del Mercado',
                'description' => 'Newsletter mensual con nuevas propiedades y noticias del mercado inmobiliario',
                'category' => 'newsletter',
                'status' => 'active',
                'is_system_template' => true,
                'html_content' => '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter {{company_name}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #7c3aed; color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; background: #f8fafc; }
        .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
        .button { display: inline-block; background: #7c3aed; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; }
        .property-card { background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .news-item { border-bottom: 1px solid #e5e7eb; padding: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Newsletter {{company_name}}</h1>
            <p>{{current_date}}</p>
        </div>
        <div class="content">
            <h2>Hola {{recipient_name}},</h2>
            <p>¡Te damos la bienvenida a nuestro newsletter mensual! Aquí encontrarás las últimas novedades del mercado inmobiliario.</p>
            
            <h3>🏠 Propiedades Destacadas</h3>
            <div class="property-card">
                <h4>Casa Moderna en Zona Residencial</h4>
                <p>3 habitaciones, 2 baños, jardín privado</p>
                <p><strong>Precio: $2,500,000</strong></p>
                <a href="#" class="button">Ver Detalles</a>
            </div>
            
            <h3>📊 Tendencias del Mercado</h3>
            <div class="news-item">
                <h4>El mercado inmobiliario muestra signos de recuperación</h4>
                <p>Los precios se han estabilizado y la demanda está aumentando gradualmente...</p>
            </div>
            
            <h3>💡 Consejos del Mes</h3>
            <div class="news-item">
                <h4>5 cosas a considerar antes de comprar una propiedad</h4>
                <p>Ubicación, precio, condición de la propiedad, financiamiento y plusvalía son factores clave...</p>
            </div>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="#" class="button">Ver Todas las Propiedades</a>
            </p>
            
            <p>¡Gracias por ser parte de nuestra comunidad!</p>
        </div>
        <div class="footer">
            <p>{{company_name}} - {{current_date}}</p>
            <p><a href="{{unsubscribe_url}}">Darse de baja</a></p>
        </div>
    </div>
</body>
</html>',
                'text_content' => 'Newsletter {{company_name}} - {{current_date}}

Hola {{recipient_name}},

¡Te damos la bienvenida a nuestro newsletter mensual!

PROPIEDADES DESTACADAS:
- Casa Moderna en Zona Residencial
  3 habitaciones, 2 baños, jardín privado
  Precio: $2,500,000

TENDENCIAS DEL MERCADO:
- El mercado inmobiliario muestra signos de recuperación
- Los precios se han estabilizado y la demanda está aumentando

CONSEJOS DEL MES:
- 5 cosas a considerar antes de comprar una propiedad
- Ubicación, precio, condición, financiamiento y plusvalía

¡Gracias por ser parte de nuestra comunidad!

{{company_name}} - {{current_date}}
Darse de baja: {{unsubscribe_url}}',
                'created_by' => $createdBy
            ],
            [
                'name' => 'Confirmación de Cita',
                'subject' => 'Confirmación de cita - {{lead_first_name}}, te esperamos mañana',
                'description' => 'Template para confirmar citas programadas con leads',
                'category' => 'notification',
                'status' => 'active',
                'is_system_template' => true,
                'html_content' => '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Cita</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc2626; color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; background: #f8fafc; }
        .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
        .appointment-details { background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .button { display: inline-block; background: #dc2626; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; }
        .important { background: #fef2f2; border: 1px solid #fecaca; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📅 Confirmación de Cita</h1>
        </div>
        <div class="content">
            <h2>Hola {{lead_first_name}},</h2>
            <p>Te confirmamos tu cita programada para visitar la propiedad.</p>
            
            <div class="appointment-details">
                <h3>Detalles de la Cita</h3>
                <p><strong>Fecha:</strong> [FECHA DE LA CITA]</p>
                <p><strong>Hora:</strong> [HORA DE LA CITA]</p>
                <p><strong>Dirección:</strong> [DIRECCIÓN DE LA PROPIEDAD]</p>
                <p><strong>Agente:</strong> {{assigned_agent_name}}</p>
                <p><strong>Contacto del agente:</strong> [TELÉFONO DEL AGENTE]</p>
            </div>
            
            <div class="important">
                <h4>🔔 Recordatorio Importante</h4>
                <ul>
                    <li>Por favor llega 5 minutos antes</li>
                    <li>Trae una identificación oficial</li>
                    <li>Si necesitas cancelar, avísanos con 24h de anticipación</li>
                </ul>
            </div>
            
            <p style="text-align: center; margin: 30px 0;">
                <a href="#" class="button">Confirmar Asistencia</a>
                <a href="#" style="background: #6b7280; display: inline-block; background: #6b7280; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin-left: 10px;">Reprogramar</a>
            </p>
            
            <p>¡Esperamos verte pronto!</p>
        </div>
        <div class="footer">
            <p>{{company_name}} - {{current_date}}</p>
            <p><a href="{{unsubscribe_url}}">Darse de baja</a></p>
        </div>
    </div>
</body>
</html>',
                'text_content' => 'Confirmación de Cita

Hola {{lead_first_name}},

Te confirmamos tu cita programada para visitar la propiedad.

DETALLES DE LA CITA:
- Fecha: [FECHA DE LA CITA]
- Hora: [HORA DE LA CITA]
- Dirección: [DIRECCIÓN DE LA PROPIEDAD]
- Agente: {{assigned_agent_name}}
- Contacto: [TELÉFONO DEL AGENTE]

RECORDATORIO IMPORTANTE:
- Por favor llega 5 minutos antes
- Trae una identificación oficial
- Si necesitas cancelar, avísanos con 24h de anticipación

¡Esperamos verte pronto!

{{company_name}} - {{current_date}}
Darse de baja: {{unsubscribe_url}}',
                'created_by' => $createdBy
            ]
        ];

        foreach ($templates as $template) {
            EmailTemplate::firstOrCreate(
                ['name' => $template['name']],
                $template
            );
        }

        $this->command->info('Email templates created successfully.');
    }
}