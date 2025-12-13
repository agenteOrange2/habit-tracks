# Requirements Document

## Introduction

Este documento define los requisitos para convertir las categorías, dificultades y la integración de Google Calendar en datos propiedad de cada usuario individual. Actualmente estos datos son globales/compartidos, pero cada usuario debe poder gestionar sus propios datos de forma independiente.

## Glossary

- **User**: Usuario autenticado en el sistema
- **Category**: Categoría para clasificar hábitos (ej: Salud, Trabajo, Estudio)
- **Difficulty**: Nivel de dificultad de un hábito que determina puntos ganados
- **Google Calendar Token**: Credenciales OAuth individuales del usuario para acceder a su Google Calendar
- **OAuth**: Protocolo de autorización que permite a usuarios vincular sus cuentas de Google

## Requirements

### Requirement 1: Categorías por Usuario

**User Story:** As a user, I want to create and manage my own categories, so that I can organize my habits according to my personal preferences.

#### Acceptance Criteria

1. WHEN a user creates a category THEN the System SHALL associate that category with the user's account
2. WHEN a user views categories THEN the System SHALL display only categories belonging to that user
3. WHEN a user edits a category THEN the System SHALL verify ownership before allowing modifications
4. WHEN a user deletes a category THEN the System SHALL remove only that user's category without affecting other users
5. WHEN a new user registers THEN the System SHALL create default categories for that user

### Requirement 2: Dificultades por Usuario

**User Story:** As a user, I want to define my own difficulty levels, so that I can customize the point system according to my goals.

#### Acceptance Criteria

1. WHEN a user creates a difficulty level THEN the System SHALL associate that difficulty with the user's account
2. WHEN a user views difficulties THEN the System SHALL display only difficulties belonging to that user
3. WHEN a user edits a difficulty THEN the System SHALL verify ownership before allowing modifications
4. WHEN a user deletes a difficulty THEN the System SHALL remove only that user's difficulty without affecting other users
5. WHEN a new user registers THEN the System SHALL create default difficulty levels for that user

### Requirement 3: Google Calendar OAuth Individual

**User Story:** As a user, I want to connect my personal Google Calendar account, so that I can sync my events without requiring admin configuration.

#### Acceptance Criteria

1. WHEN a user initiates Google Calendar connection THEN the System SHALL redirect to Google OAuth with user-specific state
2. WHEN Google OAuth completes successfully THEN the System SHALL store tokens associated with that specific user
3. WHEN a user syncs calendar events THEN the System SHALL use only that user's stored tokens
4. WHEN a user disconnects Google Calendar THEN the System SHALL remove only that user's tokens
5. WHEN tokens expire THEN the System SHALL refresh tokens using the user's stored refresh token

### Requirement 4: Aislamiento de Datos

**User Story:** As a user, I want my data to be completely isolated from other users, so that my categories, difficulties and calendar settings are private.

#### Acceptance Criteria

1. WHEN querying categories THEN the System SHALL filter by authenticated user_id
2. WHEN querying difficulties THEN the System SHALL filter by authenticated user_id
3. WHEN accessing calendar tokens THEN the System SHALL verify user ownership
4. IF a user attempts to access another user's data THEN the System SHALL deny access and return authorization error
