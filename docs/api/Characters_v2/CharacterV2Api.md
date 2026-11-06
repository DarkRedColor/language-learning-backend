## Character Api

---  

- #### URL
    1. /api/v2/characters

- #### Method
    1. Get

- #### URL Params (header)

    1. #### Required:
        1. Authorization (jwt authorization token from python )

    2. #### Optional:
        1. None

- #### Data Params

    1. icons_scale = [one of [S, M, L]] - required

- #### Success Response
    1. Code: 200
    2. Content: look at "CharacterV2ApiResponse.json"
- #### Error Response
    1. Code: 400
    2. Content: "bad icons_scale"
    3. something wrong (when is wrong version)
- #### Sample Call

  1. https://langly-api-development-development.azurewebsites.net/api/v2/characters?icons_scale=S

- #### Notes