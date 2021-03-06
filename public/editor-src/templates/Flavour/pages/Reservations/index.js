module.exports = {
  id: "FlavourReservations",
  thumbnailWidth: 680,
  thumbnailHeight: 782,
  title: "Reservations", 
  keywords: "reservations, flavour, Food, restaurant, catering, delivery, dining",
  cat: [0, 10],
  pro: true,
  resolve: { blocks:[
    {
        type: "Section",
        value: {
            _styles: [
                "section"
            ],
            items: [
                {
                    type: "SectionItem",
                    value: {
                        _styles: [
                            "section-item"
                        ],
                        items: [
                            {
                                type: "Wrapper",
                                value: {
                                    _styles: [
                                        "wrapper",
                                        "wrapper--richText"
                                    ],
                                    items: [
                                        {
                                            type: "RichText",
                                            value: {
                                                _styles: [
                                                    "richText"
                                                ],
                                                text: "<p class=\"brz-tp-heading6 brz-text-lg-center brz-text-xs-center\"><span class=\"brz-cp-color2\">BOOK A TABLE</span></p>"
                                            }
                                        }
                                    ],
                                    marginBottom: 10,
                                    marginBottomSuffix: "px",
                                    margin: 0,
                                    animationName: "fadeInDown",
                                    tempAnimationName: "fadeInDown",
                                    animationDuration: 900,
                                    animationDelay: 0
                                }
                            },
                            {
                                type: "Wrapper",
                                value: {
                                    _styles: [
                                        "wrapper",
                                        "wrapper--richText"
                                    ],
                                    items: [
                                        {
                                            type: "RichText",
                                            value: {
                                                _styles: [
                                                    "richText"
                                                ],
                                                text: "<h1 class=\"brz-text-xs-center brz-tp-heading2 brz-text-lg-center\"><span class=\"brz-cp-color2\">Request Reservation</span></h1>"
                                            }
                                        }
                                    ],
                                    marginTop: 0,
                                    marginTopSuffix: "px",
                                    margin: 0,
                                    marginBottom: 10,
                                    marginBottomSuffix: "px",
                                    animationName: "fadeInUp",
                                    tempAnimationName: "fadeInUp",
                                    animationDuration: 900,
                                    animationDelay: 0
                                }
                            },
                            {
                                type: "Wrapper",
                                value: {
                                    _styles: [
                                        "wrapper",
                                        "wrapper--richText"
                                    ],
                                    items: [
                                        {
                                            type: "RichText",
                                            value: {
                                                _styles: [
                                                    "richText"
                                                ],
                                                text: "<p class=\"brz-text-xs-center brz-tp-paragraph brz-text-lg-center\"><span class=\"brz-cp-color2\">Vivamus hendrerit arcu erat molestie vehicula. Sed auctor nequeu tellus rhoncus retut eleifcibendend nibh porttitor. Ut in nulla enim haret mirolestie magna non lorem</span></p>"
                                            }
                                        }
                                    ],
                                    marginRight: 300,
                                    marginRightSuffix: "px",
                                    margin: 0,
                                    marginLeft: 300,
                                    marginLeftSuffix: "px"
                                }
                            }
                        ],
                        paddingType: "ungrouped",
                        paddingTop: 60,
                        paddingBottom: 70,
                        padding: 75
                    }
                }
            ],
            _thumbnailSrc: 472777,
            _thumbnailWidth: 600,
            _thumbnailHeight: 104,
            _thumbnailTime: 1575280941864
        },
        blockId: "Blank000Light"
    },
    {
        type: "Section",
        value: {
            _styles: [
                "section"
            ],
            items: [
                {
                    type: "SectionItem",
                    value: {
                        _styles: [
                            "section-item"
                        ],
                        items: [
                            {
                                type: "Wrapper",
                                value: {
                                    _styles: [
                                        "wrapper",
                                        "wrapper--iconText"
                                    ],
                                    items: [
                                        {
                                            type: "Form2",
                                            value: {
                                                _styles: [
                                                    "form"
                                                ],
                                                items: [
                                                    {
                                                        type: "Form2Fields",
                                                        value: {
                                                            items: [
                                                                {
                                                                    type: "Form2Field",
                                                                    value: {
                                                                        type: "Text",
                                                                        label: "NAME ON RESERVATION",
                                                                        required: "on",
                                                                        options: [
                                                                            "Option 1",
                                                                            "Option 2"
                                                                        ],
                                                                        width: 50,
                                                                        placeholder: "NAME ON RESERVATION"
                                                                    }
                                                                },
                                                                {
                                                                    type: "Form2Field",
                                                                    value: {
                                                                        type: "Email",
                                                                        label: "EMAIL ADDRESS",
                                                                        required: "on",
                                                                        options: [
                                                                            "Option 1",
                                                                            "Option 2"
                                                                        ],
                                                                        width: 50,
                                                                        placeholder: "EMAIL ADDRESS"
                                                                    }
                                                                },
                                                                {
                                                                    type: "Form2Field",
                                                                    value: {
                                                                        type: "Date",
                                                                        label: "DATE",
                                                                        required: "on",
                                                                        options: [
                                                                            "Option 1",
                                                                            "Option 2"
                                                                        ],
                                                                        width: 30,
                                                                        placeholder: "DATE"
                                                                    }
                                                                },
                                                                {
                                                                    type: "Form2Field",
                                                                    value: {
                                                                        type: "Number",
                                                                        label: "PARTY OF",
                                                                        required: "on",
                                                                        options: [
                                                                            "Option 1",
                                                                            "Option 2"
                                                                        ],
                                                                        width: 20,
                                                                        placeholder: "PARTY OF",
                                                                        min: "1"
                                                                    }
                                                                },
                                                                {
                                                                    type: "Form2Field",
                                                                    value: {
                                                                        type: "Tel",
                                                                        label: "PHONE NUMBER",
                                                                        required: "on",
                                                                        options: [
                                                                            "Option 1",
                                                                            "Option 2"
                                                                        ],
                                                                        width: 50,
                                                                        placeholder: "PHONE NUMBER"
                                                                    }
                                                                },
                                                                {
                                                                    type: "Form2Field",
                                                                    value: {
                                                                        type: "Paragraph",
                                                                        label: "MESSAGE",
                                                                        required: "off",
                                                                        options: [
                                                                            "Option 1",
                                                                            "Option 2"
                                                                        ],
                                                                        placeholder: "MESSAGE"
                                                                    }
                                                                }
                                                            ],
                                                            tabsState: "",
                                                            tabsColor: "",
                                                            bgColorHex: "#ffffff",
                                                            bgColorOpacity: 0,
                                                            tempBgColorOpacity: 1,
                                                            bgColorPalette: "",
                                                            tempBgColorPalette: "",
                                                            borderRadius: 0,
                                                            borderTopLeftRadius: 0,
                                                            borderTopRightRadius: 0,
                                                            borderBottomLeftRadius: 0,
                                                            borderBottomRightRadius: 0,
                                                            tempBorderTopLeftRadius: 0,
                                                            tempBorderTopRightRadius: 0,
                                                            tempBorderBottomLeftRadius: 0,
                                                            tempBorderBottomRightRadius: 0,
                                                            colorPalette: "color4",
                                                            tempColorPalette: "color4",
                                                            colorOpacity: 1,
                                                            colorHex: "#f1f0f2",
                                                            tempColorOpacity: 1,
                                                            borderColorPalette: "color4",
                                                            tempBorderColorPalette: "color4",
                                                            borderColorOpacity: 1,
                                                            borderStyle: "solid",
                                                            borderWidth: 1,
                                                            borderTopWidth: 1,
                                                            borderRightWidth: 1,
                                                            borderBottomWidth: 1,
                                                            borderLeftWidth: 1,
                                                            tempBorderTopWidth: 1,
                                                            tempBorderRightWidth: 1,
                                                            tempBorderBottomWidth: 1,
                                                            tempBorderLeftWidth: 1,
                                                            fontStyle: "button",
                                                            mobileSize: "small",
                                                            mobilePaddingTop: 10,
                                                            mobilePaddingRight: 20,
                                                            mobilePaddingBottom: 10,
                                                            mobilePaddingLeft: 20
                                                        }
                                                    },
                                                    {
                                                        type: "Button",
                                                        value: {
                                                            _styles: [
                                                                "button",
                                                                "submit"
                                                            ],
                                                            text: "SUBMIT",
                                                            iconName: "",
                                                            iconType: "",
                                                            fontStyle: "heading6",
                                                            tabsState: "",
                                                            tabsColor: "",
                                                            colorPalette: "color2",
                                                            colorOpacity: 1,
                                                            borderWidth: 0,
                                                            tempBorderWidth: 2,
                                                            paddingRight: 42,
                                                            paddingLeft: 42,
                                                            borderRadiusType: "custom",
                                                            fillType: "filled",
                                                            borderRadius: 0,
                                                            borderColorOpacity: 0,
                                                            borderColorPalette: 0,
                                                            bgColorOpacity: 1,
                                                            bgColorPalette: "color3",
                                                            hoverBgColorOpacity: 1,
                                                            tempBorderRadiusType: "custom",
                                                            hoverBorderColorOpacity: 0.8,
                                                            tempBorderRadius: 0,
                                                            hoverBgColorPalette: "color4",
                                                            hoverBorderColorPalette: "",
                                                            tempHoverBorderColorPalette: "",
                                                            hoverBgColorHex: "#f1f0f2",
                                                            tempHoverBgColorOpacity: 1,
                                                            tempHoverBgColorPalette: "color3",
                                                            hoverBorderColorHex: "#f1f0f2",
                                                            hoverColorPalette: "color2",
                                                            hoverColorOpacity: 1
                                                        }
                                                    }
                                                ],
                                                padding: 15,
                                                paddingRight: 15,
                                                paddingBottom: 15,
                                                paddingLeft: 15,
                                                submitWidth: 20,
                                                tabletSubmitWidth: 100,
                                                mobilePadding: 4,
                                                mobilePaddingRight: 4,
                                                mobilePaddingBottom: 4,
                                                mobilePaddingLeft: 4,
                                                mobileHorizontalAlign: "center",
                                                mobileSubmitWidth: 100
                                            }
                                        }
                                    ],
                                    mobileMarginRightSuffix: "px",
                                    mobileMarginRight: -25,
                                    mobileMargin: 0,
                                    mobileMarginSuffix: "px",
                                    mobileMarginBottomSuffix: "px",
                                    mobileMarginBottom: 10,
                                    mobileMarginLeftSuffix: "px",
                                    mobileMarginLeft: -25
                                }
                            }
                        ],
                        bgColorPalette: "color1",
                        bgColorHex: "#0d0d0d",
                        bgColorOpacity: 0.7,
                        tempBgColorOpacity: 0.7,
                        bgImageWidth: 1920,
                        bgImageHeight: 900,
                        bgImageSrc: "733c933c65b08a58c4aa4179bedcfc16.jpg",
                        bgPositionX: 50,
                        bgPositionY: 50,
                        tempMobileBgColorOpacity: 1,
                        paddingType: "ungrouped",
                        paddingTop: 140,
                        paddingBottom: 140,
                        padding: 75,
                        containerSize: 70,
                        bgPopulation: ""
                    }
                }
            ],
            _thumbnailSrc: 472778,
            _thumbnailWidth: 600,
            _thumbnailHeight: 229,
            _thumbnailTime: 1575280797576
        },
        blockId: "Blank000Light"
    },
    {
        type: "Section",
        value: {
            _styles: [
                "section"
            ],
            items: [
                {
                    type: "SectionItem",
                    value: {
                        _styles: [
                            "section-item"
                        ],
                        items: [
                            {
                                type: "Wrapper",
                                value: {
                                    _styles: [
                                        "wrapper",
                                        "wrapper--map"
                                    ],
                                    items: [
                                        {
                                            type: "Map",
                                            value: {
                                                _styles: [
                                                    "map"
                                                ],
                                                height: 500,
                                                mobileHeight: 300
                                            }
                                        }
                                    ],
                                    marginTop: 0,
                                    marginTopSuffix: "px",
                                    margin: 0,
                                    marginBottom: 0,
                                    marginBottomSuffix: "px"
                                }
                            }
                        ],
                        padding: 0,
                        paddingTop: 0,
                        paddingBottom: 0,
                        containerType: "fullWidth"
                    }
                }
            ],
            _thumbnailSrc: 472780,
            _thumbnailWidth: 600,
            _thumbnailHeight: 160,
            _thumbnailTime: 1575280556568
        },
        blockId: "Blank000Light"
    }
]}
};