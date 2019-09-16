(function e(t, n, r) {
  function s(o, u) {
    if (!n[o]) {
      if (!t[o]) {
        var a = typeof require == "function" && require;
        if (!u && a) return a(o, !0);
        if (i) return i(o, !0);
        var f = new Error("Cannot find module '" + o + "'");
        throw ((f.code = "MODULE_NOT_FOUND"), f);
      }
      var l = (n[o] = {
        exports: {}
      });
      t[o][0].call(
        l.exports,
        function(e) {
          var n = t[o][1][e];
          return s(n ? n : e);
        },
        l,
        l.exports,
        e,
        t,
        n,
        r
      );
    }
    return n[o].exports;
  }
  var i = typeof require == "function" && require;
  for (var o = 0; o < r.length; o++) s(r[o]);
  return s;
})(
  {
    1: [
      function(require, module, exports) {
        module.exports = {
          default: require("core-js/library/fn/json/stringify"),
          __esModule: true
        };
      },
      {
        "core-js/library/fn/json/stringify": 2
      }
    ],
    2: [
      function(require, module, exports) {
        var core = require("../../modules/_core");
        var $JSON =
          core.JSON ||
          (core.JSON = {
            stringify: JSON.stringify
          });
        module.exports = function stringify(it) {
          // eslint-disable-line no-unused-vars
          return $JSON.stringify.apply($JSON, arguments);
        };
      },
      {
        "../../modules/_core": 3
      }
    ],
    3: [
      function(require, module, exports) {
        var core = (module.exports = {
          version: "2.5.3"
        });
        if (typeof __e == "number") __e = core; // eslint-disable-line no-undef
      },
      {}
    ],
    4: [
      function(require, module, exports) {
        "use strict";

        function getInputs(data) {
          var div = document.createElement("div");

          for (var el in data) {
            if (data.hasOwnProperty(el)) {
              var input = document.createElement("input");

              input.name = el;
              input.value = data[el];

              div.appendChild(input);
            }
          }

          return div;
        }

        module.exports = function(data, opts) {
          var form = document.createElement("form");

          if (typeof data !== "object" || typeof opts !== "object") {
            throw new TypeError("Expected an object");
          }

          for (var el in opts) {
            if (opts.hasOwnProperty(el)) {
              form[el] = opts[el];
            }
          }

          form.style.display = "none";
          form.appendChild(getInputs(data));

          document.body.appendChild(form);
          form.submit();
        };
      },
      {}
    ],
    5: [
      function(require, module, exports) {
        "use strict";

        Object.defineProperty(exports, "__esModule", {
          value: true
        });

        var _stringify = require("babel-runtime/core-js/json/stringify");

        var _stringify2 = _interopRequireDefault(_stringify);

        var _submitform = require("submitform");

        var _submitform2 = _interopRequireDefault(_submitform);

        function _interopRequireDefault(obj) {
          return obj && obj.__esModule
            ? obj
            : {
                default: obj
              };
        }

        var DOMAIN = "resolvepay.com";
        var HOSTS = {
          development: "http://localhost:2222",
          sandbox: "https://app-sandbox." + DOMAIN,
          production: "https://app." + DOMAIN
        };

        window.resolve = {
          checkout: function checkout(data) {
            var host = this.getHost(data);
            (0, _submitform2.default)(
              {
                data: (0, _stringify2.default)(data)
              },
              {
                action: host + "/api/checkouts",
                method: "post"
              }
            );
          },
          getHost: function getHost(data) {
            var env = "production";
            if (data.sandbox === true) {
              env = "sandbox";
            } else if (data.development) {
              env = "development";
            }
            if (!HOSTS[env]) {
              throw new Error("Invalid env specified: " + env);
            }
            return HOSTS[env];
          }
        };

        exports.default = window.resolve;
      },
      {
        "babel-runtime/core-js/json/stringify": 1,
        submitform: 4
      }
    ]
  },
  {},
  [5]
);
