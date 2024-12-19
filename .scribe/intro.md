# Introduction

API Documentation for EzexStore Backend System

<aside>
    <strong>Base URL</strong>: <code>https://backend.ezexstore.com</code>
</aside>

Esta documentación proporciona toda la información necesaria para trabajar con la API de EzexStore.

La API utiliza autenticación mediante API Keys y maneja datos específicos por tenant.

## Autenticación
- Todas las peticiones deben incluir el header `X-API-KEY`
- Las API Keys tienen scopes específicos que determinan los permisos

## Scopes Disponibles
- products:read - Lectura de productos
- products:write - Creación/Modificación de productos
- inventory:read - Consulta de inventario
- inventory:write - Modificación de inventario

