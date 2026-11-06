## Text Api

---  
Return json data about all characters.

- #### URL

    1. /api/v2/text

- #### Method
  1.Get

- #### URL Params (header)

    1. #### Required:
        1. Authorization (jwt authorization token from python )
    2. #### Optional:
        1. None

- #### Data Params
    1. lcid

- #### Success Response
    1. Code: 200
    2. Content: look at "TextApiResponse.json"
- #### Error Response
    1. Code: 400
    2. Content: "bad icons_scale"
- #### Sample Call
    1. https://langly-api-development-development.azurewebsites.net/api/v2/text?lcid=en

- #### Notes