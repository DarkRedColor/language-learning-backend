## Title (to complete)

---  

- #### URL

1. /api/v2/scene

- #### Method
    - Get

- #### URL Params (header)

    1. #### Required:
        1. Authorization (jwt authorization token from python )
    2. #### Optional:
        1. None


- #### Data Params
    1. icons_scale = [one of [S, M, L]] - required
    2. lcid = [lcid of wanted language (en by default)]
    3. sub-chapter_id= [id of scene] - required
    4. web = [one of [0, 1], by default it is 0] - optional
- #### Success Response
    1. Code: 200
    2. Content: look at "SceneV2Response.json"
- #### Error Response
    1. Code: 400
    2. Content: "bad sub-chapter_id" or "bad lcid" or "bad icons_scale" or "There are no dialogue linked"
- #### Sample Call
    1. https://langly-api-development-development.azurewebsites.net/api/v2/scene?icons_scale=S&lcid=ru-RU&sub-chapter_id=9

- #### Notes