# MijdasAuth
OAuth 2.0 compliant authorisation server

## Routes
* Base URL: https://accounts.mijdas.com/
* Login: https://accounts.mijdas.com/api/login/
    * POST
    * Headers
        * Accept: application/json
    * form-data
        * username
        * password
        * scopes
* Register: https://accounts.mijdas.com/api/register/
    * POST
    * Headers
        * Accept: application/json
    * form-data
        * username
        * email
        * password
        * c_password
        * name
        * scopes
* Check token scopes: https://accounts.mijdas.com/api/check_token/
    * POST
    * Headers
        * Accept: application/json
        * Authorization: Bearer {access_token}
    * form-data
        * scopes
