define({ "api": [
  {
    "type": "post",
    "url": "/apps/create",
    "title": "创建应用",
    "group": "Apps",
    "name": "_apps_create",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>应用名称</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "description",
            "description": "<p>应用描述</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"操作成功\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AppsController.php",
    "groupTitle": "Apps"
  },
  {
    "type": "get",
    "url": "/common/getTimestamp",
    "title": "获取服务端时间戳",
    "group": "Common",
    "name": "_common_getTimestamp",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"请求成功\",\n    \"data\": {\n        \"_timestamp\":1595403944\n    }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/CommonController.php",
    "groupTitle": "Common"
  },
  {
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "optional": false,
            "field": "varname1",
            "description": "<p>No type.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "varname2",
            "description": "<p>With type.</p>"
          }
        ]
      }
    },
    "type": "",
    "url": "",
    "version": "0.0.0",
    "filename": "./apidoc/main.js",
    "group": "D__dev_IM_apidoc_main_js",
    "groupTitle": "D__dev_IM_apidoc_main_js",
    "name": ""
  },
  {
    "type": "get",
    "url": "/chat/getAllNewMessage",
    "title": "获取新消息总数",
    "group": "chat",
    "name": "_chat_getAllNewMessage",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>用户token</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"请求成功\",\n    \"data\": {\n               \"count\": 11\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/ChatController.php",
    "groupTitle": "聊天类"
  },
  {
    "type": "get",
    "url": "/chat/getConversationInfo",
    "title": "获取双方信息",
    "group": "chat",
    "name": "_chat_getConversationInfo",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>用户token</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "target_uid",
            "description": "<p>目标用户</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"請求成功\",\n    \"data\": {\n        \"user\": {\n            \"id\": 183,\n            \"app_id\": 1,\n            \"uid\": \"268\",\n            \"nickname\": \"591test1 小姐\",\n            \"avatar\": \"https://p1.debug.591.com.hk/avatar/crop/2019/09/02/156739416662856507_90x90.jpg\",\n            \"created_at\": \"2019-09-09 17:28:37\",\n            \"updated_at\": \"2019-10-09 15:06:37\",\n            \"token\": \"\"\n        },\n        \"target\": {\n            \"nickname\": \"劉小姐\",\n            \"avatar\": \"https://statics.591.com.hk/user/images/default_user_portrait.png\",\n            \"uid\": \"79\"\n        }\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/ChatController.php",
    "groupTitle": "聊天类"
  },
  {
    "type": "post",
    "url": "/chat/lastMsgClear",
    "title": "删除会话数据",
    "group": "chat",
    "name": "_chat_lastMsgClear",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>用户token</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "days",
            "defaultValue": "30",
            "description": "<p>保留的天数</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "limit",
            "defaultValue": "100",
            "description": "<p>保留的条数</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"请求成功\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/ChatController.php",
    "groupTitle": "聊天类"
  },
  {
    "type": "get",
    "url": "/chat/onlineStatus",
    "title": "获取联系人列表在线状态",
    "group": "chat",
    "name": "_chat_onlineStatus",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>用户token</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "uids",
            "description": "<p>所有联系人uid，json [&quot;11&quot;,&quot;22&quot;]</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"请求成功\",\n    \"data\": {\n               \"uid1\": 1,\n               \"uid2\": 0,\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/ChatController.php",
    "groupTitle": "聊天类"
  },
  {
    "type": "get",
    "url": "/chat/onlineStatusByUids",
    "title": "获取联系人列表在线状态",
    "group": "chat",
    "name": "_chat_onlineStatusByUids",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "app_key",
            "description": "<p>平台key</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "app_secret",
            "description": "<p>平台密钥</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "uids",
            "description": "<p>所有联系人uid，json [&quot;11&quot;,&quot;22&quot;]</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"请求成功\",\n    \"data\": {\n               \"uid1\": 1,\n               \"uid2\": 0,\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/ChatController.php",
    "groupTitle": "聊天类"
  },
  {
    "type": "post",
    "url": "/chat/readMsg",
    "title": "消息设置已读",
    "group": "chat",
    "name": "_chat_readMsg",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>用户token</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "target_uid",
            "description": "<p>当前联系人uid</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"请求成功\",\n    \"data\": true\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/ChatController.php",
    "groupTitle": "聊天类"
  },
  {
    "type": "get",
    "url": "/chat/users",
    "title": "聊天界面联系人列表",
    "group": "chat",
    "name": "_chat_users",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>用户token</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "limit",
            "defaultValue": "20",
            "description": "<p>每页条数</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>页码</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"请求成功\",\n    \"data\": [\n        {\n            \"last_time\": \"消息发送时间\",\n            \"nickname\": \"昵称\",\n            \"avatar\": \"头像\",\n            \"is_online\": 1,\n            \"uid\":目标id,\n            \"new_msg_count\": 0,\n            \"content\":{\n                  'content':'最后一条消息',\n                  'extra':''\n            },\n           \"type\": \"消息类型\"\n        }\n    ],\n    \"total\":4\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/ChatController.php",
    "groupTitle": "聊天类"
  },
  {
    "type": "delete",
    "url": "/config/delete",
    "title": "删除配置项",
    "group": "config",
    "name": "_config_delete",
    "version": "1.0.0",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "nonce",
            "description": "<p>随机数</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "time-stamp",
            "description": "<p>时间戳</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "sign",
            "description": "<p>签名</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "app-key",
            "description": "<p>appKey</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "config_key",
            "description": "<p>配置key</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"请求成功\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/ConfigController.php",
    "groupTitle": "配置类"
  },
  {
    "type": "get",
    "url": "/config/msgWordsBlacklist",
    "title": "消息黑名单关键词",
    "group": "config",
    "name": "_config_msgWordsBlacklist",
    "version": "1.0.0",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "nonce",
            "description": "<p>随机数</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "time-stamp",
            "description": "<p>时间戳</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "sign",
            "description": "<p>签名</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "app-key",
            "description": "<p>appKey</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"请求成功\",\n    \"data\": {\n       \"config\": {}\n     }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/ConfigController.php",
    "groupTitle": "配置类"
  },
  {
    "type": "post",
    "url": "/config/msgWordsBlacklist/edit",
    "title": "添加、编辑消息黑名单关键词",
    "group": "config",
    "name": "_config_msgWordsBlacklist_edit",
    "version": "1.0.0",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "nonce",
            "description": "<p>随机数</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "time-stamp",
            "description": "<p>时间戳</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "sign",
            "description": "<p>签名</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "app-key",
            "description": "<p>appKey</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "json",
            "optional": false,
            "field": "config_value",
            "description": "<p>配置数据</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"请求成功\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/ConfigController.php",
    "groupTitle": "配置类"
  },
  {
    "type": "get",
    "url": "/data/messages",
    "title": "消息數據列表",
    "group": "data",
    "name": "_data_messages",
    "version": "1.0.0",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "nonce",
            "description": "<p>随机数</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "time-stamp",
            "description": "<p>时间戳</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "sign",
            "description": "<p>签名</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "app-key",
            "description": "<p>appKey</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "start_time",
            "description": "<p>開始時間，非必填，eg：2020-01-03 12:08:20</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "end_time",
            "description": "<p>結束時間，非必填，eg：2020-01-04 12:08:20</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "conversation",
            "description": "<p>會話標識，非必填，發送人uid與接收人uid拼接，eg：123,456</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "page",
            "description": "<p>頁碼</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "size",
            "description": "<p>每頁條數，默認30，可選值：10,15,20,25,30,50,80,100,200,300,500,1000</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "need_count",
            "description": "<p>是否需要返回總條數，1=是，0=否，默認0</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"请求成功\",\n    \"data\": {\n       \"messages\": [],\n       \"count\": 0,\n       \"page\": 1,\n       \"size\": 30\n     }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/DataController.php",
    "groupTitle": "數據类"
  },
  {
    "type": "post",
    "url": "/messages/delLiaisonPerson",
    "title": "删除联络人",
    "group": "messages",
    "name": "_messages_delLiaisonPerson",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "target_uid",
            "description": "<p>被删者uid</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"操作成功\",\n    \"data\": true\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/MessagesController.php",
    "groupTitle": "消息类"
  },
  {
    "type": "get",
    "url": "/messages/getHistoricalMessage",
    "title": "获取历史消息",
    "group": "messages",
    "name": "_messages_getHistoricalMessage",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "link_user",
            "description": "<p>聊天对象</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "node_marker",
            "description": "<p>查询标记</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "limit",
            "defaultValue": "10",
            "description": "<p>每次拉取条数</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"请求成功\",\n    \"data\": {\n        \"data\": [\n            {\n                \"msg_id\": \"f001017e-26d3-4366-9555-a709151453ac\",\n                \"from_uid\": \"1\",\n                \"target_uid\": \"3\",\n                \"type\": \"msg:text\",\n                \"content\": \"123\",\n                \"send_time\": \"2019-08-26 14:37:21\",\n                \"status\": 0,\n                \"arrivals_callback\": 1,\n                \"id\": 87,\n                \"created_at\": \"2019-08-26 14:37:21\",\n                \"read\":1, //0未读1已读\n            }\n        ],\n        \"total\": 1\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/MessagesController.php",
    "groupTitle": "消息类"
  },
  {
    "type": "post",
    "url": "/messages/messageArrival",
    "title": "到达回调",
    "group": "messages",
    "name": "_messages_messageArrival",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "msg_id",
            "description": "<p>消息id</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"操作成功\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/MessagesController.php",
    "groupTitle": "消息类"
  },
  {
    "type": "post",
    "url": "/messages/messageSynchronization",
    "title": "消息同步",
    "group": "messages",
    "name": "_messages_messageSynchronization",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>同步到用户的token</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "from_uid",
            "description": "<p>被同步者uid</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "limit",
            "defaultValue": "365",
            "description": "<p>同步的天数</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"操作成功\",\n    \"data\": true\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/MessagesController.php",
    "groupTitle": "消息类"
  },
  {
    "type": "post",
    "url": "/messages/messageTransfer",
    "title": "旧消息迁移到IM",
    "group": "messages",
    "name": "_messages_messageTransfer",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "from_uid",
            "description": "<p>发送者</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "target_uid",
            "description": "<p>接受者</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>类型</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "content",
            "description": "<p>消息体</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "created_at",
            "description": "<p>发送时间</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "app_id",
            "description": "<p>应用id</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"操作成功\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/MessagesController.php",
    "groupTitle": "消息类"
  },
  {
    "type": "post",
    "url": "/messages/onlineNotice",
    "title": "上线广播",
    "group": "messages",
    "name": "_messages_onlineNotice",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"操作成功\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/MessagesController.php",
    "groupTitle": "消息类"
  },
  {
    "type": "post",
    "url": "/messages/pictureUpload",
    "title": "图片上传",
    "group": "messages",
    "name": "_messages_pictureUpload",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "file",
            "optional": false,
            "field": "picture",
            "description": "<p>图片</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"操作成功\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/MessagesController.php",
    "groupTitle": "消息类"
  },
  {
    "type": "post",
    "url": "/messages/send",
    "title": "发消息",
    "group": "messages",
    "name": "_messages_send",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>消息类型</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "target_uid",
            "description": "<p>接收者id</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "content",
            "description": "<p>内容</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "push",
            "defaultValue": "1",
            "description": "<p>是否推送:0否1是</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "device",
            "description": "<p>设备</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "version",
            "description": "<p>版本号</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "_appid",
            "description": "<p>_appid</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "_randomstr",
            "description": "<p>随机数</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "_timestamp",
            "description": "<p>时间戳(s)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "_signature",
            "description": "<p>签名</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"消息發送成功\",\n    \"data\": {\n        \"msg_id\": \"消息uid\",\n        \"from_uid\": \"接受者id\",\n        \"target_uid\": \"发送者id\",\n        \"type\": \"消息类型\",\n        \"content\": \"消息内容\",\n        \"send_time\": \"发送时间\",\n        \"status\": '状态',\n        \"arrivals_callback\": \"消息是否需要回传,0否1是\",\n        \"message_direction\":\"1是自己发出的消息,2是接收的消息\",\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/MessagesController.php",
    "groupTitle": "消息类"
  },
  {
    "type": "post",
    "url": "/messages/sendByApps",
    "title": "发消息(从业务端发起)",
    "group": "messages",
    "name": "_messages_sendByApps",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "from_uid",
            "description": "<p>发送者</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>消息类型</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "target_uid",
            "description": "<p>接收者id</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "content",
            "description": "<p>内容</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": true,
            "field": "push",
            "defaultValue": "1",
            "description": "<p>是否推送:0否1是</p>"
          }
        ]
      }
    },
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "nonce",
            "description": "<p>随机数</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "time-stamp",
            "description": "<p>时间戳</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "sign",
            "description": "<p>签名</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "app-key",
            "description": "<p>appKey</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"消息發送成功\",\n    \"data\": {\n        \"msg_id\": \"消息uid\",\n        \"from_uid\": \"接受者id\",\n        \"target_uid\": \"发送者id\",\n        \"type\": \"消息类型\",\n        \"content\": \"消息内容\",\n        \"send_time\": \"发送时间\",\n        \"status\": '状态',\n        \"arrivals_callback\": \"消息是否需要回传,0否1是\",\n        \"message_direction\":\"1是自己发出的消息,2是接收的消息\",\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/MessagesController.php",
    "groupTitle": "消息类"
  },
  {
    "type": "post",
    "url": "/server/info",
    "title": "查看websocket连接数",
    "group": "server",
    "name": "_server_getAllUidCount",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"操作成功\",\n    \"data\": {\n        \"count\": \"用户连接数\",\n        \"websocket_count\":\"websocket连接数\"\n    }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/ServerController.php",
    "groupTitle": "server"
  },
  {
    "type": "post",
    "url": "/server/info",
    "title": "查看server连接情况",
    "group": "server",
    "name": "_server_info",
    "version": "1.0.0",
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"操作成功\",\n    \"data\": {\n        \"start_time\": \"服务器启动的时间\",\n        \"connection_num\": \"当前连接的数量\",\n        \"accept_count\": \"接受了多少个连接\",\n        \"close_count\": \"关闭的连接数量\",\n        \"tasking_num\": \"当前正在排队的任务数\",\n        \"request_count\": \"Server 收到的请求次数\",\n        \"worker_request_count\": \"当前 Worker 进程收到的请求次数\",\n        \"coroutine_num\": \"当前协程数量 coroutine_num\"\n    }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/ServerController.php",
    "groupTitle": "server"
  },
  {
    "type": "post",
    "url": "/users/block",
    "title": "拉黑",
    "group": "users",
    "name": "_users_block",
    "version": "1.0.0",
    "description": "<p>被拉黑用戶不能发送消息(提示发送成功,但是消息没有被处理)</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "uid",
            "description": "<p>被拉黑的用户id</p>"
          }
        ]
      }
    },
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "nonce",
            "description": "<p>随机数</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "time-stamp",
            "description": "<p>时间戳</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "sign",
            "description": "<p>签名</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "app-key",
            "description": "<p>appKey</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"操作成功\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/UsersController.php",
    "groupTitle": "用户类"
  },
  {
    "type": "post",
    "url": "/users/register",
    "title": "注册",
    "group": "users",
    "name": "_users_register",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "uid",
            "description": "<p>用户id</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "nickname",
            "description": "<p>用户昵称</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "avatar",
            "description": "<p>用户头像链接</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "extend",
            "description": "<p>扩展字段,json字符串</p>"
          }
        ]
      }
    },
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "nonce",
            "description": "<p>随机数</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "time-stamp",
            "description": "<p>时间戳</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "sign",
            "description": "<p>签名</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "app-key",
            "description": "<p>appKey</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"操作成功\",\n    \"data\": {\n        \"token\": \"da7a2578b70c62855bb01d2fc4323482733f1b0e\"\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/UsersController.php",
    "groupTitle": "用户类"
  },
  {
    "type": "post",
    "url": "/users/unBlock",
    "title": "解除拉黑",
    "group": "users",
    "name": "_users_unBlock",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "uid",
            "description": "<p>被拉黑的用户id</p>"
          }
        ]
      }
    },
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "nonce",
            "description": "<p>随机数</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "time-stamp",
            "description": "<p>时间戳</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "sign",
            "description": "<p>签名</p>"
          },
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "app-key",
            "description": "<p>appKey</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"操作成功\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/UsersController.php",
    "groupTitle": "用户类"
  },
  {
    "type": "get",
    "url": "/welcomes/content",
    "title": "获取用户的欢迎语",
    "group": "welcomes",
    "name": "_welcomes_content",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>用户token</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "uid",
            "description": "<p>uid</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"请求成功\",\n    \"data\": \"欢迎语内容\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/WelcomesController.php",
    "groupTitle": "欢迎语"
  },
  {
    "type": "post",
    "url": "/welcomes/del",
    "title": "删除欢迎语",
    "group": "welcomes",
    "name": "_welcomes_del",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>用户token</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"请求成功\",\n    \"data\": true\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/WelcomesController.php",
    "groupTitle": "欢迎语"
  },
  {
    "type": "get",
    "url": "/welcomes/myContent",
    "title": "获取我的欢迎语",
    "group": "welcomes",
    "name": "_welcomes_myContent",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>用户token</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"请求成功\",\n    \"data\": \"欢迎语内容\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/WelcomesController.php",
    "groupTitle": "欢迎语"
  },
  {
    "type": "post",
    "url": "/welcomes/set",
    "title": "设置欢迎语",
    "group": "welcomes",
    "name": "_welcomes_set",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>用户token</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "content",
            "description": "<p>欢迎语</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 200,\n    \"message\": \"请求成功\",\n    \"data\": true\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "{",
          "content": "{\n    \"code\": 4001,\n    \"message\": \"错误提示\",\n    \"data\": null\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/WelcomesController.php",
    "groupTitle": "欢迎语"
  }
] });
