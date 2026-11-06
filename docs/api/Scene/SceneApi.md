## Scene Api response   

---  

Return json data about selected scene.   

- #### URL  

  1. /api/scene

- #### Method

    1. Get

- #### URL Params

    1. #### Required:
       2. None
    2. #### Optional:
       3. None

- #### Data Params

  1. icons_scale = [one of [S, M, L]] - required 
  2. lcid = [lcid of wanted language (en by default)]
  3. sub-chapter_id= [id of scene] - required

- #### Success Response

  1. Code: 200
  2. Content: look at "sceneResponse.json"

- #### Error Response

    1. Code: 400
    2. Content: "bad sub-chapter_id" or "bad lcid" or "bad icons_scale" or "There are no dialogue linked"

- #### Sample Call

    1. https://langly-api-development-development.azurewebsites.net/api/scene?icons_scale=S&lcid=ru-RU&sub-chapter_id=106

- #### Notes  

  1. Now (9 december 2022) the link from sample call is created for debug maybe in future will be deleted 

