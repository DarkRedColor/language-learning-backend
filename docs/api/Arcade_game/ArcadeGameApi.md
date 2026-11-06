## Arcade Game

---  

- #### URL
    1. /api/arcade-games

- #### Method
  1.Get

- #### URL Params (header)

    1. #### Required:
        1. Authorization (jwt authorization token from python )
    2. #### Optional:
        1. None

- #### Data Params

1. lcid
2. type [-1,0,1]
3. icons_scale = [one of [S, M, L]]

- #### Success Response
    1. Code: 200
    2. Content: look at "ArcadeGamesResponse.json"
- #### Error Response
    1. Code: 400
    2. Content: "bad icons_scale"
    3. Content: "bad lcid"
    4. Content: "not enough params"
- #### Sample Call
  1. https://langly-api-development-development.azurewebsites.net/api/arcade-games?lcid=en&type=-1&icons_scale=M

- #### Notes