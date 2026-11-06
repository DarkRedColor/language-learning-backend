## Character Api response

---  

Return json data about all characters.

- #### URL

    1. /api/characters

- #### Method

    1. Get

- #### URL Params

    1. #### Required:
        2. None
    2. #### Optional:
        3. None

- #### Data Params

    1. icons_scale = [one of [S, M, L]] - required

- #### Success Response

    1. Code: 200
    2. Content: look at "charactersResponse.json"

- #### Error Response

    1. Code: 400
    2. Content: "bad icons_scale"

- #### Sample Call

    1. https://langly-api-development-development.azurewebsites.net/api/characters?icons_scale=S

- #### Notes

    1. In Future ( Now: 9 december 2022) response from Sample Call (charactersResponse.json) may  change

