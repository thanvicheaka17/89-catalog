# API Documentation


Base URL: https://example.com/api

X-API-KEY: edff951e464a5290515173df1d8a761cd27dcc55531d6f622fcb834fdf74c441

Common error codes
- `422` — Validation errors (response includes `errors` object).
- `401` — Unauthorized (invalid credentials or token).
- `403` — Forbidden (e.g., account deactivated).

Endpoints

---

**Login**
- **Method:** POST
- **Path:** `/login`
- **Auth / Middleware:** `api.key` (see project middleware)
- **Request Body (JSON):**
  - `email` (string, required)
  - `password` (string, required)
  - `trust_device` (boolean, optional) — Whether to trust this device for future logins
- **Success Response (2FA NOT enabled):**
  ```json
  { "access_token": "<jwt>", "token_type": "Bearer", "expires_in": 3600 }
  ```
- **Success Response (2FA enabled):**
  ```json
  {
    "success": true,
    "message": "Two-factor authentication required",
    "requires_2fa": true,
    "temp_token": "<encrypted_temp_token>"
  }
  ```
- **Failure (401):** invalid credentials.
- **Failure (403):** if the account exists but is deactivated.
- **Note:** When `trust_device` is true, the device will be added to trusted devices list for easier future logins.
- **2FA Flow:** If user has 2FA enabled, login will return `requires_2fa: true` with a `temp_token`. Frontend must then call `/user/2fa/verify` with the `temp_token` and `one_time_password` to complete login.

Example (Normal Login):
```
curl -X POST "https://example.com/api/login" \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: your_api_key_here" \
  -d '{"email":"email@example.com","password":"password123","trust_device":true}'
```

Example (2FA Enabled - Step 1):
```
curl -X POST "https://example.com/api/login" \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: your_api_key_here" \
  -d '{"email":"email@example.com","password":"password123"}'
```

Response:
```json
{
  "success": true,
  "message": "Two-factor authentication required",
  "requires_2fa": true,
  "temp_token": "eyJpdiI6Ik1qWTJNVEUyTkR..."
}
```

---

**Verify 2FA (Login)**
- **Method:** POST
- **Path:** `/user/2fa/verify`
- **Auth / Middleware:** Public (no authentication required)
- **Request Body (JSON):**
  - `temp_token` (string, required) — Temporary token received from login endpoint when 2FA is enabled
  - `one_time_password` (string, required, 6 digits) — 2FA code from authenticator app
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Login successful",
    "access_token": "<jwt>",
    "token_type": "Bearer",
    "expires_in": 3600
  }
  ```
- **Failure (401):** Invalid 2FA code, expired token, or invalid temp_token.
- **Note:** This endpoint is used during login flow. For verifying 2FA when already authenticated, use the same endpoint without `temp_token` (requires authentication).

Example:
```
curl -X POST "https://example.com/api/user/2fa/verify" \
  -H "Content-Type: application/json" \
  -d '{"temp_token":"eyJpdiI6Ik1qWTJNVEUyTkR...","one_time_password":"123456"}'
```

---


**Get Authenticated User**
- **Method:** GET
- **Path:** `/me`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Returns the authenticated user's profile data.
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "name": "Alice",
      "email": "alice@example.com",
      "avatar": "https://.../avatars/1.jpg",
      "role": "user",
      "account_status": true,
      "phone_number": "234567890",
      "country_code":"+1",
      "location": "New York",
      "birth_date": "1990-01-01",
      "last_login_at": "2025-01-01T10:00:00Z",
      "login_count": 5,
      "member_since": "2025-12-25T12:34:56Z",
      "two_factor_enabled": false,
      "login_notifications": true,
      "email_notifications": true,
      "sms_notifications": false,
      "push_notifications": false,
      "language": "en",
      "timezone": "UTC",
      "created_at": "2025-12-25T12:34:56Z"
    }
  }
  ```

Example:
```
curl "https://example.com/api/me" -H "Authorization: Bearer <jwt>"
```

---


**Register User**
- **Method:** POST
- **Path:** `/register`
- **Auth / Middleware:** Public
- **Request Body (JSON):**
  - `name` (string, required)
  - `email` (string, required, unique)
  - `password` (string, required, min:8)
  - `confirm_password` (string, required, must match `password`)
- **Success Response (201):**
  ```json
  {
    "success": true,
    "message": "User registered successfully",
    "data": {
      "user": { "id": 1, "name": "...", "email": "...", "role": "user" },
      "access_token": "<jwt>",
      "token_type": "Bearer",
      "expires_in": 3600
    }
  }
  ```
- **Validation Error (422):** contains `errors` object with field messages.

Example:
```
curl -X POST "https://example.com/api/register" \
  -H "Content-Type: application/json" \
  -d '{"name":"Alice","email":"alice@example.com","password":"secret123","confirm_password":"secret123"}'
```

---

**Forgot Password**
- **Method:** POST
- **Path:** `/forgot-password`
- **Auth / Middleware:** Public
- **Request Body (JSON):**
  - `email` (string, required)
- **Response:**
  ```json
  { "success": true, "message": "Password reset link sent." }
  ```
  The `message` reflects the framework status string; `success` is true when the reset link was sent.

Example:
```
curl -X POST "https://example.com/api/forgot-password" \
  -H "Content-Type: application/json" \
  -d '{"email":"alice@example.com"}'
```

---

**Reset Password**
- **Method:** POST
- **Path:** `/reset-password`
- **Auth / Middleware:** Public
- **Request Body (JSON):**
  - `email` (string, required)
  - `token` (string, required) - **Encrypted token from reset password URL** (send as-is, do not decrypt on frontend)
  - `password` (string, required, min:8)
  - `password_confirmation` (string, required)
- **Success Response:**
  ```json
  { "success": true, "message": "Password has been reset." }
  ```
- **Failure (422):** returns `{ "success": false, "message": "..." }` with 422 status for invalid token or validation errors.
- **Important:** The `token` parameter is encrypted for security. Extract it directly from the URL query parameter and send it to the API without modification. The backend will decrypt it automatically.


---

**Change Password**
- **Method:** POST
- **Path:** `/change-password`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Changes the authenticated user's password.
- **Request Body (JSON):**
  - `current_password` (string, required) — Current password
  - `new_password` (string, required, min:8) — New password
  - `confirm_new_password` (string, required) — Must match new_password
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "Password changed successfully."
  }
  ```
- **Validation Error (422):** returns `errors` object with field messages.
- **Failure (400):** Current password incorrect, new password same as current, or passwords don't match.

Example:
```
curl -X POST "https://example.com/api/change-password" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{
    "current_password": "oldpassword123",
    "new_password": "newpassword123",
    "confirm_new_password": "newpassword123"
  }'
```

---

**Update Account**
- **Method:** POST
- **Path:** `/update-account`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Updates the authenticated user's account information.
- **Request Body (JSON):**
  - `name` (string, required, max:255)
  - `email` (string, required, email, unique, max:255)
  - `phone_number` (string, nullable, max:255)
  - `location` (string, nullable, max:255)
  - `birth_date` (date, nullable, before:today)
  - `two_factor_enabled` (boolean, nullable)
  - `login_notifications` (boolean, nullable)
  - `email_notifications` (boolean, nullable)
  - `sms_notifications` (boolean, nullable)
  - `push_notifications` (boolean, nullable)
  - `language` (string, nullable, max:255)
  - `timezone` (string, nullable, max:255)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "Account updated successfully"
  }
  ```
- **Validation Error (422):** returns `errors` object with field messages.

Example:
```
curl -X POST "https://example.com/api/update-account" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Name",
    "email": "newemail@example.com",
    "phone_number": "+1234567890",
    "location": "New York",
    "birth_date": "1990-01-01",
    "login_notifications": true,
    "email_notifications": true
  }'
```

---

**Delete Account**
- **Method:** DELETE
- **Path:** `/delete-account`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Permanently deletes the authenticated user's account and all related data.
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "Account deleted successfully"
  }
  ```
- **Note:** This action cannot be undone. All user data, game plays, achievements, friends, and devices will be deleted.

Example:
```
curl -X DELETE "https://example.com/api/delete-account" \
  -H "Authorization: Bearer <jwt>"
```

---

**Refresh Token**
- **Method:** POST
- **Path:** `/refresh`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Refreshes the JWT and returns a new token payload.
- **Success Response:** same as login: `{ "access_token": "<jwt>", "token_type": "Bearer", "expires_in": 3600 }`

Example:
```
curl -X POST "https://example.com/api/refresh" \
  -H "Authorization: Bearer <jwt>"
```

---

Authenticated (JWT) endpoints

All endpoints below require a valid JWT in the `Authorization` header, e.g. `Authorization: Bearer <token>`. They also require the `check.status` middleware which ensures the user account is active.

**Logout**
- **Method:** POST
- **Path:** `/logout`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Invalidates the current JWT.
- **Success Response:**
  ```json
  { "success": true, "message": "Successfully logged out" }
  ```

Example:
```
curl -X POST "https://example.com/api/logout" \
  -H "Authorization: Bearer <jwt>"
```

---


**Get Promotion Banners**
- **Method:** GET
- **Path:** `/promo`
- **Auth / Middleware:** `api.key`
- **Description:** Returns active promotion banners. A banner is returned when `is_active` is true and (if date fields exist) the current date falls within `start_date` and `end_date` rules. Results are ordered by `priority` and `created_at`.
- **Query Parameters (optional):**
  - `page` (integer) — page number when pagination is enabled (e.g. `?page=2`).
    - If the API is configured to return paginated results, supply `page` to fetch additional pages. The response will include the current page of items and may include pagination metadata (total, per_page, current_page, last_page, etc.).
  - `per_page` (integer) — number of items to return per page (e.g. `?per_page=50`).
    - Use `per_page` together with `page` to control the size of each page. Common values used in the admin UI are 25, 50, 100 and 200; your API may accept other values.
    - Example combined usage: `?page=1&per_page=50`.
- **Success Response (200):**
  The `/promo` endpoint returns the `data` array of banners and, when pagination is enabled, it also returns pagination metadata at the top level. Example paginated response:

  ```json
  {
    "success": true,
    "data": [
      {
        "id": "77a1833e-e222-11f0-a251-6c3c8c1c0e83",
        "title": "234234234",
        "message": null,
        "buttonText": "Learn More",
        "buttonUrl": null,
        "imageUrl": null,
        "position": "top",
        "priority": 0,
        "isActive": true,
        "startAt": null,
        "expiresAt": null,
        "styles": {
          "backgroundGradientType": "solid",
          "backgroundStyle": "#0f172a",
          "textColor": "#ffffff",
          "buttonGradientType": "solid",
          "buttonStyle": "#f59e0b",
          "buttonTextColor": "#ffffff"
        }
      }
    ],
    "total": 240,
    "current_page": 3,
    "last_page": 240,
    "per_page": 1
  }
  ```

  Pagination fields:
  - `total`: total number of available items across all pages.
  - `current_page`: the current page number returned.
  - `last_page`: the last available page number.
  - `per_page`: number of items per page in this response.

Example (first page):
```
curl "https://example.com/api/promo" -H "X-API-KEY: your_api_key_here"
```

Example (page 2):
```
curl "https://example.com/api/promo?page=2" -H "X-API-KEY: your_api_key_here"
```

Example (page 1, 50 items per page):
```
curl "https://example.com/api/promo?page=1&per_page=50" -H "X-API-KEY: your_api_key_here"
```

---

**Get Banners**
- **Method:** GET
- **Path:** `/banners`
- **Auth / Middleware:** `api.key`
- **Description:** Get a paginated list of banners.
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 25)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "title": "Welcome Banner",
        "subtitle": "Welcome to our platform",
        "status": true,
        "image": "https://example.com/banner.jpg",
        "link": "https://example.com/link",
        "priority": 1
      }
    ],
    "total": 10,
    "current_page": 1,
    "last_page": 1,
    "per_page": 25
  }
  ```

**Get Providers**
- **Method:** GET
- **Path:** `/providers`
- **Auth / Middleware:** `api.key`
- **Description:** Get a paginated list of game providers.
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 25)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "name": "Pragmatic Play",
        "slug": "pragmatic-play",
        "description": "Leading game provider",
        "logo": "https://example.com/logo.jpg"
      }
    ],
    "total": 20,
    "current_page": 1,
    "last_page": 1,
    "per_page": 25
  }
  ```

**Get Testimonials**
- **Method:** GET
- **Path:** `/testimonials`
- **Auth / Middleware:** `api.key`
- **Description:** Get a paginated list of user testimonials.
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 25)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "user_name": "John Doe",
        "user_role": "Premium User",
        "avatar": "https://example.com/avatar.jpg",
        "message": "Great platform!",
        "rating": 5,
        "is_featured": true,
        "is_active": true
      }
    ],
    "total": 50,
    "current_page": 1,
    "last_page": 2,
    "per_page": 25
  }
  ```

**Get RTP Games**
- **Method:** GET
- **Path:** `/rtp-games`
- **Auth / Middleware:** `api.key`
- **Description:** Get a paginated list of RTP games with filtering and sorting options.
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 25)
  - `provider` (string) — Filter by provider slug
  - `sort` (string) — Sort options: "rtp_high", "rtp_low", "rating_high", "rating_low", "newest" (default: "rtp_high")
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "name": "Sweet Bonanza",
        "slug": "sweet-bonanza",
        "rtp": 96.5,
        "rating": 4.8,
        "image": "https://example.com/game.jpg",
        "provider": "Pragmatic Play",
        "category": "Slots"
      }
    ],
    "total": 100,
    "current_page": 1,
    "last_page": 4,
    "per_page": 25
  }
  ```

**Get Casino Categories**
- **Method:** GET
- **Path:** `/casino-categories`
- **Auth / Middleware:** `api.key`
- **Description:** Get a paginated list of casino categories.
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 25)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "slug": "online-casinos",
        "name": "Online Casinos",
        "description": "Best online casinos",
        "logo": "https://example.com/category-logo.jpg"
      }
    ],
    "total": 10,
    "current_page": 1,
    "last_page": 1,
    "per_page": 25
  }
  ```

**Get Casinos**
- **Method:** GET
- **Path:** `/casinos`
- **Auth / Middleware:** `api.key`
- **Description:** Get a paginated list of casinos with filtering and sorting options.
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 25)
  - `category` (string) — Filter by category slug
  - `sort` (string) — Sort options: "rtp_high", "rtp_low", "wd_high", "wd_low", "newest" (default: "newest")
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "name": "Casino Name",
        "slug": "casino-slug",
        "description": "Casino description",
        "logo": "https://example.com/casino-logo.jpg",
        "rtp": 96.5,
        "daily_withdrawal_amount": 100000,
        "min_deposit": 100,
        "max_withdrawal": 50000,
        "category": "Online Casinos"
      }
    ],
    "total": 50,
    "current_page": 1,
    "last_page": 2,
    "per_page": 25
  }
  ```

---


**Get Login Notifications**
- **Method:** GET
- **Path:** `/login-notifications`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Returns the user's login activity history and device information.
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "title": "Login from Chrome on Windows",
        "message": "You logged in from Chrome on Windows at 192.168.1.1",
        "created_at": "2025-01-01T10:00:00Z"
      }
    ]
  }
  ```

Example:
```
curl "https://example.com/api/login-notifications" \
  -H "Authorization: Bearer <jwt>"
```

---

**Get Trusted Devices**
- **Method:** GET
- **Path:** `/trusted-devices`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Returns a paginated list of the user's trusted devices.
- **Query Parameters (optional):**
  - `page` (integer) — page number (default: 1)
  - `per_page` (integer) — items per page (default: 25)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "device_name": "Chrome on Windows",
        "ip_address": "192.168.1.1",
        "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36...",
        "last_active_at": "2025-01-01T10:00:00Z",
        "revoked": false,
        "device_fingerprint": "a1b2c3d4..."
      }
    ],
    "total": 3,
    "current_page": 1,
    "last_page": 1,
    "per_page": 25
  }
  ```

Example:
```
curl "https://example.com/api/trusted-devices?page=1&per_page=10" \
  -H "Authorization: Bearer <jwt>"
```

---

**Revoke Trusted Device**
- **Method:** POST
- **Path:** `/trusted-devices/revoke/{id}`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Revokes access for a specific trusted device.
- **Path Parameters:**
  - `id` (integer, required) — Device ID
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "Device access revoked successfully"
  }
  ```
- **Failure (404):** Device not found or doesn't belong to user.

Example:
```
curl -X POST "https://example.com/api/trusted-devices/revoke/1" \
  -H "Authorization: Bearer <jwt>"
```

---

**Revoke All Trusted Devices**
- **Method:** POST
- **Path:** `/trusted-devices/revoke-all`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Revokes access for all trusted devices except the current one.
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "All trusted devices revoked successfully"
  }
  ```

Example:
```
curl -X POST "https://example.com/api/trusted-devices/revoke-all" \
  -H "Authorization: Bearer <jwt>"
```

---

**Enable Two-Factor Authentication (Start Setup)**
- **Method:** POST
- **Path:** `/user/2fa/enable`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Starts the 2FA setup process. Generates a secret and QR code, but **does NOT enable 2FA yet**. User must verify the code using `/user/2fa/complete-setup` to complete the setup. This prevents users from being locked out if they abandon the setup.
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "Please scan the QR code and verify with a 6-digit code to complete setup.",
    "secret": "JBSWY3DPEHPK3PXP",
    "qr_code_url": "otpauth://totp/Example:user@example.com?secret=JBSWY3DPEHPK3PXP&issuer=Example",
    "setup_pending": true
  }
  ```
- **Failure (400):** 2FA is already enabled.
- **Note:** 
  - Use the QR code URL to generate a QR code for authenticator apps, or manually enter the secret key.
  - After scanning, call `/user/2fa/complete-setup` with the 6-digit code to finish setup.
  - If user leaves without completing, call `/user/2fa/cancel-setup` to clean up.

Example:
```
curl -X POST "https://example.com/api/user/2fa/enable" \
  -H "Authorization: Bearer <jwt>"
```

---

**Complete 2FA Setup**
- **Method:** POST
- **Path:** `/user/2fa/complete-setup`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Completes the 2FA setup by verifying the code. **Only after successful verification will 2FA be enabled.** This is the second step after calling `/user/2fa/enable`.
- **Request Body (JSON):**
  - `one_time_password` (string, required, 6 digits) — 2FA code from authenticator app
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "Two-factor authentication has been enabled successfully."
  }
  ```
- **Failure (400):** No pending setup found or 2FA already enabled.
- **Failure (401):** Invalid 2FA code.
- **Validation Error (422):** Invalid OTP format.

Example:
```
curl -X POST "https://example.com/api/user/2fa/complete-setup" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{"one_time_password": "123456"}'
```

---

**Cancel 2FA Setup**
- **Method:** POST
- **Path:** `/user/2fa/cancel-setup`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Cancels a pending 2FA setup. Cleans up the secret if user abandons the setup process. Only works if 2FA is not yet enabled.
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "2FA setup has been cancelled."
  }
  ```
- **Failure (400):** 2FA already enabled or no pending setup to cancel.

Example:
```
curl -X POST "https://example.com/api/user/2fa/cancel-setup" \
  -H "Authorization: Bearer <jwt>"
```

---

**Verify Two-Factor Authentication**
- **Method:** POST
- **Path:** `/user/2fa/verify`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Verifies a two-factor authentication code.
- **Request Body (JSON):**
  - `one_time_password` (string, required, digits:6) — 6-digit TOTP code
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "2FA verified successfully."
  }
  ```
- **Failure (401):** Invalid 2FA code or 2FA not enabled.
- **Validation Error (422):** Invalid OTP format.

Example:
```
curl -X POST "https://example.com/api/user/2fa/verify" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{"one_time_password": "123456"}'
```

---

**Disable Two-Factor Authentication**
- **Method:** POST
- **Path:** `/user/2fa/disable`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Disables two-factor authentication for the user. **Requires 2FA code verification** for security.
- **Request Body (JSON):**
  - `one_time_password` (string, required, 6 digits) — 2FA code from authenticator app to verify before disabling
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "2FA disabled successfully."
  }
  ```
- **Failure (400):** 2FA is not enabled.
- **Failure (401):** Invalid 2FA code.
- **Validation Error (422):** Invalid OTP format.
- **Security Note:** Users must verify their 2FA code before disabling to prevent unauthorized disabling.

Example:
```
curl -X POST "https://example.com/api/user/2fa/disable" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{"one_time_password": "123456"}'
```

---

**Update Notification Preferences**
- **Method:** POST
- **Path:** `/user/notification-preferences`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Updates user notification preferences for different types of notifications.
- **Request Body (JSON):**
  - `notification_type` (string, required) — Type: "login", "email", "sms", or "push"
  - `enable` (boolean, required) — Enable or disable the notification type
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "Notification preferences updated successfully"
  }
  ```
- **Validation Error (422):** Invalid notification type or enable value.

Example:
```
curl -X POST "https://example.com/api/user/notification-preferences" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{"notification_type": "email", "enable": true}'
```

---

## Top Tools Endpoints

**Get Top Tools**
- **Method:** GET
- **Path:** `/top-tools`
- **Auth / Middleware:** `api.key`
- **Description:** Get a paginated list of top tools with optional category filtering, sorting, and filtering options.
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 25)
  - `category` (integer) — Category ID to filter by
  - `sorting` (string) — Sort options: "most_relevant", "most_popular", "highest_rated", "price_low_to_high", "price_high_to_low" (default: "most_relevant")
  - `filters` (string) — Filter options: "premium", "best", "new", "popular", "new_releases"
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": "uuid",
        "title": "Tool Name",
        "slug": "tool-slug",
        "description": "Tool description",
        "image": "https://example.com/image.jpg",
        "rating": 4.5,
        "average_user_rating": 4.2,
        "total_user_ratings": 25,
        "user_count": 1000,
        "active_hours": 7200,
        "rank": 1,
        "badge": "premium",
        "tier": "gold",
        "price": 99.99,
        "win_rate_increase": 15.5,
        "category": "Calculator"
      }
    ],
    "total": 50,
    "current_page": 1,
    "last_page": 2,
    "per_page": 25,
    "sort_options": {
      "most_relevant": "Most Relevant",
      "most_popular": "Most Popular",
      "highest_rated": "Highest Rated",
      "price_low_to_high": "Price: Low to High",
      "price_high_to_low": "Price: High to Low"
    },
    "filter_options": {
      "premium": "Premium Only",
      "best": "Best Use",
      "new": "New Tools",
      "popular": "Popular Tools",
      "new_releases": "New Releases"
    }
  }
  ```

**Get Tool Details**
- **Method:** GET
- **Path:** `/top-tools/{slug}`
- **Auth / Middleware:** `api.key`
- **Description:** Get detailed information about a specific tool.
- **Path Parameters:**
  - `slug` (string, required) — Tool slug
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": {
      "id": "uuid",
      "title": "Tool Name",
      "slug": "tool-slug",
      "description": "Detailed tool description",
      "image": "https://example.com/image.jpg",
      "rating": 4.5,
      "average_user_rating": 4.2,
      "total_user_ratings": 25,
      "rating_distribution": { "1": 0, "2": 1, "3": 2, "4": 8, "5": 14 },
      "user_count": 1000,
      "active_hours": 7200,
      "rank": 1,
      "badge": "premium",
      "tier": "gold",
      "price": 99.99,
      "win_rate_increase": 15.5,
      "category": "Calculator",
      "user_has_rated": false,
      "user_rating": null
    }
  }
  ```

**Get Tool Categories**
- **Method:** GET
- **Path:** `/tool-categories`
- **Auth / Middleware:** `api.key`
- **Description:** Get a paginated list of tool categories.
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 25)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "name": "Calculator",
        "tool_count": 15,
        "slug": "calculator",
        "description": "Tools for calculating odds and probabilities"
      }
    ],
    "total": 10,
    "current_page": 1,
    "last_page": 1,
    "per_page": 25
  }
  ```

**Get Hot and Fresh Tools**
- **Method:** GET
- **Path:** `/hot-and-fresh`
- **Auth / Middleware:** `api.key`
- **Description:** Get a paginated list of hot and trending tools based on popularity metrics (user count, rating, active hours).
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 25)
  - `sorting` (string) — Sort options: "most_popular", "highest_rated", "rank", "price_low_to_high", "price_high_to_low" (default: "most_popular")
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "title": "Popular Tool Name",
        "slug": "popular-tool-slug",
        "description": "Tool description",
        "image": "https://example.com/image.jpg",
        "rating": 4.8,
        "user_count": 1500,
        "active_hours": 8000,
        "rank": 1,
        "badge": "premium",
        "tier": "gold",
        "price": 99.99,
        "win_rate_increase": 20.5,
        "popularity_score": 1630.8
      }
    ],
    "total": 50,
    "current_page": 1,
    "last_page": 2,
    "per_page": 25,
    "sort_options": {
      "most_popular": "Most Popular",
      "highest_rated": "Highest Rated",
      "rank": "By Rank",
      "price_low_to_high": "Price: Low to High",
      "price_high_to_low": "Price: High to Low"
    }
  }
  ```

**Get Hot and Fresh Tool Details**
- **Method:** GET
- **Path:** `/hot-and-fresh/{slug}`
- **Auth / Middleware:** `api.key`
- **Description:** Get detailed information about a specific hot and fresh tool.
- **Path Parameters:**
  - `slug` (string, required) — Tool slug
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "title": "Popular Tool Name",
      "slug": "popular-tool-slug",
      "description": "Detailed tool description",
      "image": "https://example.com/image.jpg",
      "rating": 4.8,
      "user_count": 1500,
      "active_hours": 8000,
      "rank": 1,
      "badge": "premium",
      "tier": "gold",
      "price": 99.99,
      "win_rate_increase": 20.5,
      "popularity_score": 1630.8
    }
  }
  ```
- **Failure (404):** Hot & Fresh item not found.

---

## User Tools/Boosters Endpoints

All endpoints require `auth:api` and `check.status` middleware.

**Get User Tools**
- **Method:** GET
- **Path:** `/user-tools`
- **Description:** Get a paginated list of user's purchased tools/boosters.
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 25)
  - `status` (string) — Filter by status: "active", "inactive", "expired", "all" (default: "active")
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "user_tool_id": 1,
        "tool_id": 1,
        "title": "Calculator Pro",
        "slug": "calculator-pro",
        "description": "Advanced calculator tool",
        "image": "https://example.com/tool.jpg",
        "badge": "premium",
        "tier": "gold",
        "category": "Calculator",
        "win_rate_increase": 15.5,
        "status": "active",
        "purchased_at": "2025-01-01T10:00:00Z",
        "expires_at": "2025-12-31T23:59:59Z",
        "usage_count": 5,
        "max_usage": 100,
        "remaining_uses": 95,
        "price_paid": 99.99,
        "transaction_id": "TXN12345",
        "is_active": true,
        "days_until_expiry": 364
      }
    ],
    "total": 5,
    "current_page": 1,
    "last_page": 1,
    "per_page": 25
  }
  ```

**Purchase Tool**
- **Method:** POST
- **Path:** `/user-tools`
- **Description:** Purchase and add a tool to the user's collection.
- **Request Body (JSON):**
  - `tool_id` (integer, required) — Tool ID to purchase
  - `expires_at` (date, optional) — Expiration date
  - `max_usage` (integer, optional) — Maximum usage count
  - `price_paid` (numeric, optional) — Price paid for the tool
  - `transaction_id` (string, optional) — Transaction reference
  - `metadata` (array, optional) — Additional metadata
- **Success Response (201):**
  ```json
  {
    "success": true,
    "message": "Tool purchased successfully!",
    "data": {
      "user_tool_id": 1,
      "tool_id": 1,
      "status": "active",
      "purchased_at": "2025-01-01T10:00:00Z",
      "expires_at": "2025-12-31T23:59:59Z",
      "max_usage": 100,
      "price_paid": 99.99,
      "transaction_id": "TXN12345"
    }
  }
  ```
- **Failure (409):** User already has an active version of this tool.

**Get User Tool Details**
- **Method:** GET
- **Path:** `/user-tools/{id}`
- **Description:** Get detailed information about a specific user tool.
- **Path Parameters:**
  - `id` (integer, required) — User tool ID
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": {
      "user_tool_id": 1,
      "tool_id": 1,
      "title": "Calculator Pro",
      "slug": "calculator-pro",
      "description": "Advanced calculator tool",
      "image": "https://example.com/tool.jpg",
      "badge": "premium",
      "tier": "gold",
      "category": "Calculator",
      "win_rate_increase": 15.5,
      "status": "active",
      "purchased_at": "2025-01-01T10:00:00Z",
      "expires_at": "2025-12-31T23:59:59Z",
      "usage_count": 5,
      "max_usage": 100,
      "remaining_uses": 95,
      "price_paid": 99.99,
      "transaction_id": "TXN12345",
      "is_active": true,
      "days_until_expiry": 364
    }
  }
  ```

**Update User Tool**
- **Method:** PUT
- **Path:** `/user-tools/{id}`
- **Description:** Update user tool properties (admin/system use).
- **Path Parameters:**
  - `id` (integer, required) — User tool ID
- **Request Body (JSON):**
  - `status` (string, optional) — Status: "active", "inactive", "expired", "suspended"
  - `expires_at` (date, optional) — New expiration date
  - `max_usage` (integer, optional) — New maximum usage count
  - `metadata` (array, optional) — Additional metadata
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "Tool updated successfully!",
    "data": {
      "user_tool_id": 1,
      "status": "active",
      "expires_at": "2025-12-31T23:59:59Z",
      "max_usage": 100
    }
  }
  ```

**Use Tool**
- **Method:** POST
- **Path:** `/user-tools/{id}/use`
- **Description:** Use a tool and increment its usage count.
- **Path Parameters:**
  - `id` (integer, required) — User tool ID
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "Tool used successfully!",
    "data": {
      "usage_count": 6,
      "remaining_uses": 94,
      "is_active": true
    }
  }
  ```
- **Failure (403):** Tool not available for use (expired, usage limit reached, or inactive).

**Delete User Tool**
- **Method:** DELETE
- **Path:** `/user-tools/{id}`
- **Description:** Remove a tool from the user's collection.
- **Path Parameters:**
  - `id` (integer, required) — User tool ID
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "Tool removed successfully!"
  }
  ```

**Get User Tools Statistics**
- **Method:** GET
- **Path:** `/user-tools-stats`
- **Description:** Get user's tool/booster usage statistics.
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": {
      "total_tools": 15,
      "active_tools": 12,
      "expired_tools": 2,
      "inactive_tools": 1,
      "suspended_tools": 0,
      "total_spent": 1499.85
    }
  }
  ```

---

## Demo Games Endpoints

**Get Demo Games**
- **Method:** GET
- **Path:** `/demo-games`
- **Auth / Middleware:** `api.key`
- **Description:** Get a paginated list of demo games.
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 25)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "title": "Sweet Bonanza",
        "slug": "sweet-bonanza",
        "description": "Popular demo slot game",
        "image": "https://example.com/game-image.jpg",
        "category": "Slots",
        "is_demo": true
      }
    ],
    "total": 50,
    "current_page": 1,
    "last_page": 2,
    "per_page": 25
  }
  ```

**Get Demo Game Details**
- **Method:** GET
- **Path:** `/demo-games/{slug}`
- **Auth / Middleware:** `api.key`
- **Description:** Get detailed information about a specific demo game.
- **Path Parameters:**
  - `slug` (string, required) — Demo game slug
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "title": "Sweet Bonanza",
      "slug": "sweet-bonanza",
      "description": "Detailed description of the Sweet Bonanza demo game",
      "image": "https://example.com/game-image.jpg",
      "category": "Slots",
      "is_demo": true
    }
  }
  ```

---

## Tool Ratings Endpoints

**Rate Tool**
- **Method:** POST
- **Path:** `/top-tools/rating`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Submit or update a rating for a specific tool.
- **Request Body (JSON):**
  - `tool_id` (string, required) — UUID of the tool
  - `tool_slug` (string, required if tool_id not provided) — Slug of the tool
  - `rating` (integer, required, 1-5) — Rating value
  - `review` (string, nullable, max:1000) — Optional review text
- **Success Response (201):**
  ```json
  {
    "success": true,
    "message": "Rating submitted successfully",
    "data": {
      "id": 1,
      "user_id": 1,
      "tool_id": "uuid",
      "rating": 5,
      "review": "Great tool!",
      "user": { "id": 1, "name": "John Doe" }
    }
  }
  ```
- **Failure (409):** User has already rated this tool.

**Get Tool Ratings**
- **Method:** GET
- **Path:** `/top-tools/{slug}/ratings`
- **Auth / Middleware:** `api.key`
- **Description:** Get all ratings and reviews for a specific tool.
- **Path Parameters:**
  - `slug` (string, required) — Tool slug
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 10)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "user_name": "John Doe",
        "rating": 5,
        "review": "Excellent tool!",
        "created_at": "2025-01-01T10:00:00Z",
        "is_owner": false
      }
    ],
    "tool_stats": {
      "average_rating": 4.5,
      "total_ratings": 25,
      "rating_distribution": { "1": 0, "2": 1, "3": 2, "4": 8, "5": 14 }
    },
    "total": 25,
    "current_page": 1,
    "last_page": 3,
    "per_page": 10
  }
  ```

**Get My Ratings**
- **Method:** GET
- **Path:** `/my-ratings`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Get all ratings submitted by the authenticated user.
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 15)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "tool": {
          "id": "uuid",
          "name": "Tool Name",
          "slug": "tool-slug",
          "image": "https://example.com/image.jpg"
        },
        "rating": 5,
        "review": "Great tool!",
        "created_at": "2025-01-01T10:00:00Z",
        "updated_at": "2025-01-01T10:00:00Z"
      }
    ],
    "total": 10,
    "current_page": 1,
    "last_page": 1,
    "per_page": 15
  }
  ```

**Get Rating Statistics**
- **Method:** GET
- **Path:** `/ratings/stats`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Get overall rating statistics across all tools.
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": {
      "total_ratings": 150,
      "average_rating_all": 4.2,
      "rating_distribution": { "1": 5, "2": 10, "3": 20, "4": 50, "5": 65 },
      "top_rated_tools": [
        {
          "id": "uuid",
          "name": "Best Tool",
          "slug": "best-tool",
          "average_rating": 4.8,
          "total_ratings": 25,
          "image": "https://example.com/image.jpg"
        }
      ],
      "recent_ratings": [
        {
          "user_name": "John Doe",
          "tool_name": "Tool Name",
          "tool_slug": "tool-slug",
          "rating": 5,
          "created_at": "2025-01-01T10:00:00Z"
        }
      ]
    }
  }
  ```

---

## Gaming Endpoints

**Store Game Play**
- **Method:** POST
- **Path:** `/game-play`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Records a game play session for the authenticated user.
- **Request Body (JSON):**
  - `game_id` (string, required, UUID) — Valid game ID from demo_games table
  - `duration_minutes` (integer, required) — Duration in minutes
  - `played_at` (date, nullable) — When the game was played (defaults to now)
- **Success Response (201):**
  ```json
  {
    "success": true,
    "message": "Game play stored successfully",
    "data": {
      "id": 1,
      "user_id": 1,
      "game_id": "123e4567-e89b-12d3-a456-426614174000",
      "duration_minutes": 30,
      "played_at": "2025-01-01T10:00:00Z"
    }
  }
  ```
- **Validation Error (422):** Invalid game_id or validation errors.

Example:
```
curl -X POST "https://example.com/api/game-play" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{
    "game_id": "123e4567-e89b-12d3-a456-426614174000",
    "duration_minutes": 30,
    "played_at": "2025-01-01T10:00:00Z"
  }'
```

---

**Get Game Plays**
- **Method:** GET
- **Path:** `/game-play`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Returns a paginated list of the user's game play history.
- **Query Parameters (optional):**
  - `page` (integer) — page number (default: 1)
  - `per_page` (integer) — items per page (default: 25)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "game_id": "123e4567-e89b-12d3-a456-426614174000",
        "duration_minutes": 30,
        "played_at": "2025-01-01T10:00:00Z"
      }
    ],
    "total": 50,
    "current_page": 1,
    "last_page": 2,
    "per_page": 25
  }
  ```

Example:
```
curl "https://example.com/api/game-play?page=1&per_page=10" \
  -H "Authorization: Bearer <jwt>"
```

---

**Store Achievement**
- **Method:** POST
- **Path:** `/achievement`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Records or updates a user achievement.
- **Request Body (JSON):**
  - `achievement_code` (string, required) — Unique achievement code
  - `title` (string, required, max:255) — Achievement title
  - `description` (string, nullable) — Achievement description
  - `unlocked_at` (date, nullable) — When achievement was unlocked (defaults to now)
- **Success Response (201):**
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "user_id": 1,
      "achievement_code": "first_win",
      "title": "First Victory",
      "description": "Win your first game",
      "unlocked_at": "2025-01-01T10:00:00Z"
    }
  }
  ```
- **Note:** If achievement already exists, it will be updated.

Example:
```
curl -X POST "https://example.com/api/achievement" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{
    "achievement_code": "first_win",
    "title": "First Victory",
    "description": "Win your first game",
    "unlocked_at": "2025-01-01T10:00:00Z"
  }'
```

---

**Get Achievements**
- **Method:** GET
- **Path:** `/achievement`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Returns a paginated list of the user's achievements.
- **Query Parameters (optional):**
  - `page` (integer) — page number (default: 1)
  - `per_page` (integer) — items per page (default: 25)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "achievement_code": "first_win",
        "title": "First Victory",
        "description": "Win your first game",
        "unlocked_at": "2025-01-01T10:00:00Z"
      }
    ],
    "total": 10,
    "current_page": 1,
    "last_page": 1,
    "per_page": 25
  }
  ```

Example:
```
curl "https://example.com/api/achievement" \
  -H "Authorization: Bearer <jwt>"
```

---

**Add Friend**
- **Method:** POST
- **Path:** `/friend`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Sends a friend request to another user.
- **Request Body (JSON):**
  - `friend_id` (integer, required) — User ID of the friend to add
- **Success Response (201):**
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "user_id": 1,
      "friend_id": 2,
      "status": "pending",
      "accepted_at": null
    }
  }
  ```
- **Failure (400):** Cannot add yourself, already friends, or friend not found.

Example:
```
curl -X POST "https://example.com/api/friend" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{"friend_id": 2}'
```

---

**Get Friends**
- **Method:** GET
- **Path:** `/friend`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Returns a paginated list of the user's friends and friend requests.
- **Query Parameters (optional):**
  - `page` (integer) — page number (default: 1)
  - `per_page` (integer) — items per page (default: 25)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "friend_id": 2,
        "status": "pending",
        "accepted_at": null
      }
    ],
    "total": 5,
    "current_page": 1,
    "last_page": 1,
    "per_page": 25
  }
  ```

Example:
```
curl "https://example.com/api/friend" \
  -H "Authorization: Bearer <jwt>"
```

---

**Accept Friend Request**
- **Method:** POST
- **Path:** `/friend/accept`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Accepts a pending friend request.
- **Request Body (JSON):**
  - `friend_id` (integer, required) — User ID of the friend request sender
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "user_id": 1,
      "friend_id": 2,
      "status": "accepted",
      "accepted_at": "2025-01-01T10:00:00Z"
    }
  }
  ```

Example:
```
curl -X POST "https://example.com/api/friend/accept" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{"friend_id": 2}'
```

---

**Reject Friend Request**
- **Method:** POST
- **Path:** `/friend/reject`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Rejects a pending friend request.
- **Request Body (JSON):**
  - `friend_id` (integer, required) — User ID of the friend request sender
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "user_id": 1,
      "friend_id": 2,
      "status": "rejected",
      "rejected_at": "2025-01-01T10:00:00Z"
    }
  }
  ```

Example:
```
curl -X POST "https://example.com/api/friend/reject" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{"friend_id": 2}'
```

---

**Get Gaming Statistics**
- **Method:** GET
- **Path:** `/gaming-stats`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Returns aggregated gaming statistics for the authenticated user.
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": {
      "hours_played": 150,
      "games_played": 25,
      "friends": 5,
      "achievements": 8,
      "current_level": 1,
      "next_level": 2,
      "total_xp": 0,
      "xp_at_level_start": 0,
      "xp_for_next_level": 100,
      "xp_in_current_level": 0,
      "xp_needed_to_level_up": 100,
      "xp_remaining_to_level_up": 100,
      "level_progress_percent": 0
    }
  }
  ```

Example:
```
curl "https://example.com/api/gaming-stats" \
  -H "Authorization: Bearer <jwt>"
```

---

**Get Events**
- **Method:** GET
- **Path:** `/events`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Returns active events that are currently running.
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "title": "Winter Tournament",
        "isActive": true,
        "description": "Join our winter gaming tournament",
        "startAt": "2025-01-01T00:00:00Z",
        "endAt": "2025-01-31T23:59:59Z"
      }
    ]
  }
  ```

Example:
```
curl "https://example.com/api/events" \
  -H "Authorization: Bearer <jwt>"
```

---

## Notifications Endpoints

**Get Notifications**
- **Method:** GET
- **Path:** `/notifications`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Returns a paginated list of the user's notifications.
- **Query Parameters (optional):**
  - `page` (integer) — page number (default: 1)
  - `per_page` (integer) — items per page (default: 25)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "title": "✅ Withdraw Successful!",
        "message": "Congratulations! Your withdrawal of Rp 100,000 has been successfully processed.",
        "amount": 100000,
        "type": "withdraw",
        "proof_url": "https://example.com/proof/123",
        "is_read": false,
        "read_at": null,
        "created_at": "2025-01-01T10:00:00Z",
        "updated_at": "2025-01-01T10:00:00Z"
      }
    ],
    "total": 25,
    "current_page": 1,
    "last_page": 2,
    "per_page": 25
  }
  ```

Example:
```
curl "https://example.com/api/notifications?page=1&per_page=10" \
  -H "Authorization: Bearer <jwt>"
```

---

**Mark Notification as Read**
- **Method:** POST
- **Path:** `/notifications/read`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Marks a specific notification as read.
- **Request Body (JSON):**
  - `id` (integer, required) — Notification ID
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "user_id": 1,
      "title": "✅ Withdraw Successful!",
      "message": "Congratulations! Your withdrawal of Rp 100,000 has been successfully processed.",
      "amount": 100000,
      "type": "withdraw",
      "proof_url": "https://example.com/proof/123",
      "is_read": true,
      "read_at": "2025-01-01T10:05:00Z",
      "created_at": "2025-01-01T10:00:00Z",
      "updated_at": "2025-01-01T10:05:00Z"
    }
  }
  ```
- **Failure (404):** Notification not found.

Example:
```
curl -X POST "https://example.com/api/notifications/read" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{"id": 1}'
```

---

**Mark All Notifications as Read**
- **Method:** POST
- **Path:** `/notifications/mark-all-as-read`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Marks all user notifications as read.
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "All notifications marked as read"
  }
  ```

Example:
```
curl -X POST "https://example.com/api/notifications/mark-all-as-read" \
  -H "Authorization: Bearer <jwt>"
```

---

**Create Withdraw Success Notification**
- **Method:** POST
- **Path:** `/notifications/withdraw-success`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Creates a withdraw success notification and broadcasts it.
- **Request Body (JSON):**
  - `amount` (numeric, required, min:0) — Withdrawal amount
  - `proof_url` (string, nullable, url) — Proof URL
  - `broadcast_type` (string, nullable) — "public", "user_only", or "global" (default: "public")
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "Withdraw success notification created and broadcasted",
    "data": {
      "notification_id": 1,
      "broadcast_type": "public",
      "broadcast_at": "2025-01-01T10:00:00.000000Z"
    }
  }
  ```

Example:
```
curl -X POST "https://example.com/api/notifications/withdraw-success" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 100000,
    "proof_url": "https://example.com/proof/123",
    "broadcast_type": "public"
  }'
```

---

**Create Jackpot Withdraw Notification**
- **Method:** POST
- **Path:** `/notifications/jackpot-withdraw`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Creates a jackpot withdrawal notification and broadcasts it globally.
- **Request Body (JSON):**
  - `amount` (numeric, required, min:0) — Jackpot withdrawal amount
  - `proof_url` (string, nullable, url) — Proof URL
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "Jackpot withdraw notification created and broadcasted globally",
    "data": {
      "notification_id": 2,
      "broadcast_type": "global",
      "broadcast_at": "2025-01-01T10:00:00.000000Z"
    }
  }
  ```

Example:
```
curl -X POST "https://example.com/api/notifications/jackpot-withdraw" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 1000000,
    "proof_url": "https://example.com/jackpot-proof/456"
  }'
```

---

**Get Broadcast Statistics**
- **Method:** GET
- **Path:** `/notifications/broadcast-stats`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Returns statistics about notifications and broadcasts.
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": {
      "total_notifications": 150,
      "withdraw_notifications": 120,
      "jackpot_notifications": 15,
      "unread_notifications": 25,
      "today_notifications": 8
    }
  }
  ```

Example:
```
curl "https://example.com/api/notifications/broadcast-stats" \
  -H "Authorization: Bearer <jwt>"
```

---

## Chat Endpoints

**Join Chat**
- **Method:** POST
- **Path:** `/chat/join`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Joins the chat room with a username for the session.
- **Request Body (JSON):**
  - `username` (string, required, min:2, max:20) — Username for chat (alphanumeric + underscore)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "Successfully joined chat"
  }
  ```
- **Validation Error (422):** Invalid username format.

Example:
```
curl -X POST "https://example.com/api/chat/join" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{"username": "gamer123"}'
```

---

**Send Chat Message**
- **Method:** POST
- **Path:** `/chat/send`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Sends a message to the chat room with spam protection and rate limiting.
- **Request Body (JSON):**
  - `message` (string, required, min:1, max:500) — Chat message
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "Message sent successfully"
  }
  ```
- **Failure (422):** Message validation failed or contains spam.
- **Failure (429):** Rate limit exceeded (max 5 messages per minute).
- **Note:** Rate limited to 5 messages per minute. Spam detection includes URLs, repeated characters, profanity, and all caps messages.

Example:
```
curl -X POST "https://example.com/api/chat/send" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{"message": "Hello everyone!"}'
```

---

**Get Chat Messages**
- **Method:** GET
- **Path:** `/chat/messages`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Returns a paginated list of recent chat messages.
- **Query Parameters (optional):**
  - `page` (integer) — page number (default: 1)
  - `per_page` (integer) — items per page (default: 25)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "username": "gamer123",
        "message": "Hello everyone!",
        "created_at": "2025-01-01T10:00:00Z",
        "updated_at": "2025-01-01T10:00:00Z"
      }
    ],
    "total": 150,
    "current_page": 1,
    "last_page": 6,
    "per_page": 25
  }
  ```

Example:
```
curl "https://example.com/api/chat/messages?page=1&per_page=20" \
  -H "Authorization: Bearer <jwt>"
```

---

**Get Active Chat Members**
- **Method:** GET
- **Path:** `/chat/active-members`
- **Auth / Middleware:** `auth:api`, `check.status`
- **Description:** Returns the count of active chat members (users who sent messages in the last hour).
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": {
      "active_members": 12,
      "last_updated": "2025-01-01T10:00:00.000000Z"
    }
  }
  ```

Example:
```
curl "https://example.com/api/chat/active-members" \
  -H "Authorization: Bearer <jwt>"
```

---

## ZONA PROMAX HUB Analytics Endpoints

**Base Path:** `/api/zona-promax-hub`

All endpoints require `api.key` middleware.

**Get Tool Statistics**
- **Method:** GET
- **Path:** `/stats`
- **Description:** Returns tool rating, active users, and usage statistics
- **Response:**
  ```json
  {
    "success": true,
    "data": {
      "rating": 4.9,
      "active_users": 2400,
      "hours_played": 1200,
      "last_updated": "2025-01-04T10:00:00Z"
    }
  }
  ```

**Get RTP Live Chart Data**
- **Method:** GET
- **Path:** `/rtp-live-chart`
- **Query Parameters:**
  - `hours` (integer, optional) — Hours of data (default: 24)
  - `provider` (string, optional) — Filter by provider slug
- **Response:**
  ```json
  {
    "success": true,
    "data": [
      {
        "time": "14:00",
        "rtp": 94.5,
        "count": 25
      }
    ],
    "meta": {
      "hours": 24,
      "provider": null,
      "updated_at": "2025-01-04T10:00:00Z"
    }
  }
  ```

**Get Pattern Analysis Data**
- **Method:** GET
- **Path:** `/pattern-analysis`
- **Query Parameters:**
  - `provider` (string, optional) — Filter by provider slug
- **Response:**
  ```json
  {
    "success": true,
    "data": {
      "patterns": {
        "Manual": 45,
        "Auto": 35,
        "Turbo": 20
      },
      "total_spins": 100,
      "last_updated": "2025-01-04T10:00:00Z"
    }
  }
  ```

**Get Provider Performance**
- **Method:** GET
- **Path:** `/provider-performance`
- **Query Parameters:**
  - `limit` (integer, optional) — Number of providers (default: 10)
- **Response:**
  ```json
  {
    "success": true,
    "data": [
      {
        "name": "Pragmatic Play",
        "slug": "pragmatic",
        "avg_rtp": 96.2,
        "game_count": 150,
        "performance_score": 32.4
      }
    ]
  }
  ```

**Get Hot Times Schedule**
- **Method:** GET
- **Path:** `/hot-times-schedule`
- **Query Parameters:**
  - `timezone` (string, optional) — Timezone (default: "Asia/Jakarta")
- **Response:**
  ```json
  {
    "success": true,
    "data": {
      "schedule": [
        {
          "provider": "Pragmatic Play",
          "slug": "pragmatic",
          "times": [
            {
              "time": "18:00",
              "rtp": 97.5,
              "status": "hot"
            }
          ]
        }
      ],
      "current_time": "10:30 WIB"
    }
  }
  ```

**Get Live Player Data**
- **Method:** GET
- **Path:** `/live-player-data`
- **Query Parameters:**
  - `page` (integer, optional) — Page number (default: 1)
  - `per_page` (integer, optional) — Items per page (default: 25)
  - `provider` (string, optional) — Filter by provider slug
  - `search` (string, optional) — Search in game names
- **Response:**
  ```json
  {
    "success": true,
    "data": [
      {
        "no": 1,
        "id": "uuid",
        "name": "Sweet Bonanza",
        "provider": "Pragmatic Play",
        "provider_slug": "pragmatic",
        "rtp": 96.5,
        "online_today": 1250,
        "jackpot": 2500000,
        "image_url": "https://...",
        "last_updated": "2025-01-04T10:00:00Z"
      }
    ],
    "meta": {
      "current_page": 1,
      "last_page": 10,
      "per_page": 25,
      "total": 250
    }
  }
  ```

**Get Providers List**
- **Method:** GET
- **Path:** `/providers`
- **Description:** Returns list of providers for filtering
- **Response:**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "name": "Pragmatic Play",
        "slug": "pragmatic",
        "game_count": 150
      }
    ]
  }
  ```

**Refresh Data (Manual)**
- **Method:** POST
- **Path:** `/refresh`
- **Description:** Manually refresh all cached data and broadcast updates
- **Response:**
  ```json
  {
    "success": true,
    "message": "Data refreshed successfully",
    "timestamp": "2025-01-04T10:00:00Z"
  }
  ```

---

## Newsletter Subscription Endpoints

**Subscribe to Newsletter**
- **Method:** POST
- **Path:** `/newsletter-subscribers`
- **Auth / Middleware:** `api.key` (authentication optional - user_id will be null if not authenticated)
- **Description:** Subscribe an email to the newsletter. If user is authenticated, the subscription will be linked to their account.
- **Request Body (JSON):**
  - `email` (string, required, email, unique) — Email address to subscribe
- **Success Response (200):**
  ```json
  {
    "success": true,
    "message": "Newsletter subscribed successfully"
  }
  ```
- **Validation Error (422):** Email already subscribed or invalid email format.

Example:
```
curl -X POST "https://example.com/api/newsletter-subscribers" \
  -H "X-API-KEY: your_api_key_here" \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com"}'
```

Authenticated Example:
```
curl -X POST "https://example.com/api/newsletter-subscribers" \
  -H "X-API-KEY: your_api_key_here" \
  -H "Authorization: Bearer <jwt>" \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com"}'
```

---

## Global Search Endpoints

**Global Search**
- **Method:** POST
- **Path:** `/global-search`
- **Auth / Middleware:** `api.key` (authentication optional - returns different results for authenticated vs unauthenticated users)
- **Description:** Search across multiple content types (games, tools, casinos, testimonials, promotions, events, etc.). Returns categorized results grouped by content type. Authenticated users get access to more content types including RTP games, tools, and casinos.
- **Request Body (JSON):**
  - `search` (string, required, min:2) — Search query (minimum 2 characters)
  - `page` (integer, optional) — Page number (default: 1)
  - `per_page` (integer, optional) — Items per page (default: 20)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": {
      "rtp_games": [
        {
          "id": 1,
          "title": "Sweet Bonanza",
          "type": "rtp_games",
          "name": "Sweet Bonanza",
          "provider": "Pragmatic Play",
          "rtp": 96.5,
          "pola": "auto",
          "rating": 4.8,
          "image": "https://example.com/game.jpg",
          "created_at": "2025-01-01"
        }
      ],
      "demo_games": [
        {
          "id": 1,
          "title": "Demo Game",
          "type": "demo_games",
          "slug": "demo-game",
          "image": "https://example.com/demo.jpg",
          "description": "Demo game description",
          "is_demo": true,
          "created_at": "2025-01-01"
        }
      ],
      "tools": [
        {
          "id": 1,
          "title": "Tool Name",
          "type": "tools",
          "slug": "tool-slug",
          "description": "Tool description",
          "image": "https://example.com/tool.jpg",
          "rating": 4.5,
          "category": "Calculator",
          "created_at": "2025-01-01"
        }
      ]
    },
    "meta": {
      "search_query": "sweet",
      "total_results": 50,
      "current_page": 1,
      "last_page": 3,
      "per_page": 20,
      "from": 1,
      "to": 20,
      "category_counts": {
        "rtp_games": 10,
        "demo_games": 15,
        "tools": 5,
        "testimonials": 8,
        "casinos": 7,
        "promotions": 3,
        "events": 2
      }
    }
  }
  ```
- **Failure (400):** Search query too short (less than 2 characters).

**Note:** 
- Authenticated users can search: RTP games, demo games, hot & fresh items, tools, testimonials, casinos, promotions, and events.
- Unauthenticated users can search: demo games, testimonials, promotions, and events only.

Example:
```
curl -X POST "https://example.com/api/global-search" \
  -H "X-API-KEY: your_api_key_here" \
  -H "Content-Type: application/json" \
  -d '{"search": "sweet bonanza", "page": 1, "per_page": 20}'
```

---

## Blog Posts Endpoints

**Get Blog Posts**
- **Method:** GET
- **Path:** `/blog-posts`
- **Auth / Middleware:** `api.key`
- **Description:** Get a paginated list of published blog posts with optional filtering and search.
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 12, allowed: 6, 12, 24, 48)
  - `tag` (string) — Filter by tag
  - `search` (string) — Search in title, content, excerpt, and tags
  - `featured` (boolean) — Show only featured posts
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "title": "Blog Post Title",
        "slug": "blog-post-slug",
        "excerpt": "Short excerpt...",
        "content": "Full blog post content...",
        "featured_image": "https://example.com/image.jpg",
        "author_name": "John Doe",
        "author_role": "Editor",
        "tags": ["gaming", "tips", "strategy"],
        "read_time": 5,
        "view_count": 150,
        "is_featured": true,
        "published_at": "2025-01-01 10:00:00",
        "created_at": "2025-01-01 09:00:00"
      }
    ],
    "total": 50,
    "current_page": 1,
    "last_page": 5,
    "per_page": 12
  }
  ```

Example:
```
curl "https://example.com/api/blog-posts?per_page=12&tag=gaming&featured=true" \
  -H "X-API-KEY: your_api_key_here"
```

---

**Get Blog Post Details**
- **Method:** GET
- **Path:** `/blog-posts/{slug}`
- **Auth / Middleware:** `api.key`
- **Description:** Get detailed information about a specific blog post. Automatically increments view count.
- **Path Parameters:**
  - `slug` (string, required) — Blog post slug
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "title": "Blog Post Title",
      "slug": "blog-post-slug",
      "excerpt": "Short excerpt...",
      "content": "Full blog post content...",
      "featured_image": "https://example.com/image.jpg",
      "author_name": "John Doe",
      "author_role": "Editor",
      "tags": ["gaming", "tips", "strategy"],
      "read_time": 5,
      "view_count": 151,
      "is_featured": true,
      "published_at": "2025-01-01 10:00:00",
      "created_at": "2025-01-01 09:00:00"
    }
  }
  ```
- **Failure (404):** Blog post not found or not published.

Example:
```
curl "https://example.com/api/blog-posts/blog-post-slug" \
  -H "X-API-KEY: your_api_key_here"
```

---

**Get Blog Post Tags**
- **Method:** GET
- **Path:** `/blog-posts-tags`
- **Auth / Middleware:** `api.key`
- **Description:** Get a list of all unique tags from published blog posts with their usage counts, sorted by popularity (top 20).
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "name": "gaming",
        "count": 25
      },
      {
        "name": "tips",
        "count": 18
      },
      {
        "name": "strategy",
        "count": 12
      }
    ]
  }
  ```

Example:
```
curl "https://example.com/api/blog-posts-tags" \
  -H "X-API-KEY: your_api_key_here"
```

---

## RTP Provider Endpoints

**Get RTP Promax Providers**
- **Method:** GET
- **Path:** `/rtp-promax-providers`
- **Auth / Middleware:** `api.key`
- **Description:** Get a paginated list of providers that support RTP Promax.
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 25)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "slug": "pragmatic-play",
        "description": "Leading game provider",
        "rtp_promax_name": "Pragmatic Play Promax",
        "rtp_promax_logo": "https://example.com/promax-logo.jpg"
      }
    ],
    "total": 10,
    "current_page": 1,
    "last_page": 1,
    "per_page": 25
  }
  ```

Example:
```
curl "https://example.com/api/rtp-promax-providers?per_page=25" \
  -H "X-API-KEY: your_api_key_here"
```

---

**Get RTP Promax Plus Providers**
- **Method:** GET
- **Path:** `/rtp-promax-plus-providers`
- **Auth / Middleware:** `api.key`
- **Description:** Get a paginated list of providers that support RTP Promax Plus.
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 25)
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "slug": "pragmatic-play",
        "description": "Leading game provider",
        "rtp_promax_plus_name": "Pragmatic Play Promax Plus",
        "rtp_promax_plus_logo": "https://example.com/promax-plus-logo.jpg"
      }
    ],
    "total": 8,
    "current_page": 1,
    "last_page": 1,
    "per_page": 25
  }
  ```

Example:
```
curl "https://example.com/api/rtp-promax-plus-providers?per_page=25" \
  -H "X-API-KEY: your_api_key_here"
```

---

## RTP Game Endpoints (Extended)

**Get RTP Promax Games**
- **Method:** GET
- **Path:** `/rtp-promax-games`
- **Auth / Middleware:** `api.key`
- **Description:** Get a paginated list of RTP games from providers that support RTP Promax, with filtering and sorting options.
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 25)
  - `provider` (string) — Filter by provider slug
  - `sort` (string) — Sort options: "rtp_high", "rtp_low", "rating_high", "rating_low", "newest", "oldest", "most_popular", "least_popular", "most_rated", "least_rated" (default: "rtp_high")
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "name": "Sweet Bonanza",
        "provider": "Pragmatic Play",
        "rtp": 96.5,
        "pola": "auto",
        "rating": 4.8,
        "image": "https://example.com/game.jpg",
        "stake_bet": 200,
        "step_one": "Step 1",
        "step_two": "Step 2",
        "step_three": "Step 3",
        "step_four": "Step 4",
        "type_step_one": "manual",
        "type_step_two": "auto",
        "type_step_three": "turbo",
        "type_step_four": "manual",
        "description_step_one": "Description for step 1",
        "description_step_two": "Description for step 2",
        "description_step_three": "Description for step 3",
        "description_step_four": "Description for step 4"
      }
    ],
    "sort_options": {
      "rtp_high": "RTP High to Low",
      "rtp_low": "RTP Low to High",
      "rating_high": "Rating High to Low",
      "rating_low": "Rating Low to High",
      "newest": "Newest",
      "oldest": "Oldest",
      "most_popular": "Most Popular",
      "least_popular": "Least Popular",
      "most_rated": "Most Rated",
      "least_rated": "Least Rated"
    },
    "total": 100,
    "current_page": 1,
    "last_page": 4,
    "per_page": 25
  }
  ```

Example:
```
curl "https://example.com/api/rtp-promax-games?provider=pragmatic-play&sort=rtp_high&per_page=25" \
  -H "X-API-KEY: your_api_key_here"
```

---

**Get RTP Promax Plus Games**
- **Method:** GET
- **Path:** `/rtp-promax-plus-games`
- **Auth / Middleware:** `api.key`
- **Description:** Get a paginated list of RTP games from providers that support RTP Promax Plus, with filtering, sorting, and search options.
- **Query Parameters (optional):**
  - `per_page` (integer) — Items per page (default: 25)
  - `provider` (string) — Filter by provider slug
  - `search` (string) — Search in game names
  - `sort` (string) — Sort options: "rtp_high", "rtp_low", "rating_high", "rating_low", "newest", "oldest", "most_popular", "least_popular", "most_rated", "least_rated" (default: "rtp_high")
- **Success Response (200):**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "name": "Gates of Olympus",
        "provider": "Pragmatic Play",
        "rtp": 96.8,
        "pola": "auto",
        "rating": 4.9,
        "image": "https://example.com/game.jpg",
        "stake_bet": 200,
        "step_one": "Step 1",
        "step_two": "Step 2",
        "step_three": "Step 3",
        "step_four": "Step 4",
        "type_step_one": "manual",
        "type_step_two": "auto",
        "type_step_three": "turbo",
        "type_step_four": "manual",
        "description_step_one": "Description for step 1",
        "description_step_two": "Description for step 2",
        "description_step_three": "Description for step 3",
        "description_step_four": "Description for step 4"
      }
    ],
    "sort_options": {
      "rtp_high": "RTP High to Low",
      "rtp_low": "RTP Low to High",
      "rating_high": "Rating High to Low",
      "rating_low": "Rating Low to High",
      "newest": "Newest",
      "oldest": "Oldest",
      "most_popular": "Most Popular",
      "least_popular": "Least Popular",
      "most_rated": "Most Rated",
      "least_rated": "Least Rated"
    },
    "total": 80,
    "current_page": 1,
    "last_page": 4,
    "per_page": 25
  }
  ```

Example:
```
curl "https://example.com/api/rtp-promax-plus-games?search=sweet&sort=rating_high&per_page=25" \
  -H "X-API-KEY: your_api_key_here"
```

---
