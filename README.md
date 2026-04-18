# TW-CDC Travel Health

互動式台灣疾管署旅遊健康建議地圖，以視覺化方式呈現全球各國旅遊疫情建議等級。

**Demo：** [https://cclljj.github.io/TW-CDC-Travel-Health/](https://cclljj.github.io/TW-CDC-Travel-Health/)

---

## 專案簡介

本專案抓取[疾病管制署旅遊疫情建議](https://www.cdc.gov.tw/CountryEpidLevel/)資料，以互動式世界地圖搭配側欄清單，讓使用者快速掌握各國目前的旅遊疫情等級與疾病類型。

---

## 功能特色

- **互動世界地圖** — 以顏色區分旅遊建議等級，滑鼠移至國家可看到疾病種類與等級提示
- **側欄詳細資訊** — 依疾病分類列出受影響國家及對應等級，含統計數字
- **多重資料來源備援** — 本地快取 → GitHub Raw → 疾管署 API → CORS Proxy，確保資料可靠性
- **即時更新時間戳** — 顯示最後資料擷取時間（台灣時間）
- **響應式設計** — 支援桌機、平板與手機

---

## 旅遊疫情建議等級

| 等級 | 顏色 | 說明 |
|------|------|------|
| 第三級 | 紅色 | 警告（Warning）：避免所有非必要旅遊 |
| 第二級 | 橘色 | 警示（Alert）：採取加強防護措施 |
| 第一級 | 黃色 | 注意（Watch）：注意當地疫情狀況 |
| — | 綠色 | 無旅遊疫情建議 |

---

## 技術架構

| 類別 | 套件／技術 |
|------|-----------|
| UI 框架 | Bootstrap 5.3.2 |
| DOM 操作 | jQuery 3.7.1 |
| 資料視覺化 | D3.js v3、TopoJSON v1、D3 Geo Projection v0 |
| 地圖元件 | WorldMap.js（自製 choropleth 地圖元件） |
| 資料格式 | JSON（疾管署 API） |
| 部署方式 | 靜態網頁（GitHub Pages） |

---

## 專案結構

```
TW-CDC-Travel-Health/
├── index.html                  # 主頁面（含內嵌樣式與邏輯）
├── data/
│   └── travel-health.json      # 本地快取資料
├── js/
│   └── worldmap-twcdc.v1.js    # 自製世界地圖視覺化元件
└── css/
    └── worldmap-twcdc.v1.css   # 地圖樣式
```

---

## 本地開發

需要 Python 3，執行以下指令後開啟 [http://localhost:8765](http://localhost:8765)：

```bash
python3 -m http.server 8765
```

---

## 資料來源

疾管署旅遊疫情建議資料：[https://www.cdc.gov.tw/CountryEpidLevel/](https://www.cdc.gov.tw/CountryEpidLevel/)
