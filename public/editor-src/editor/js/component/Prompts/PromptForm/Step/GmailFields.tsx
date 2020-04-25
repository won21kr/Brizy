import React, { Component, ReactElement } from "react";
import { t } from "visual/utils/i18n";
import Smtp from "./common/Smtp";
import { Context, ContextIntegration } from "../../common/GlobalApps/Context";

const apiKeys = [
  {
    name: "emailTo",
    title: t("Email To"),
    required: true,
    helper: `<p class="brz-p">If you need to have multiple emails you can separate them by commas:</p>
             <p class="brz-p"><span class="brz-span">me@email.com,</span> <span class="brz-span">hi@email.com</span></p>`
  },
  { name: "subject", title: t("Subject") },
  { name: "fromEmail", title: t("From Email") },
  { name: "fromName", title: t("From Name") },
  { name: "replayTo", title: t("Reply-To") },
  { name: "cc", title: t("Cc") },
  { name: "bcc", title: t("Bcc") },
  {
    name: "metaData",
    title: t("Meta Data"),
    type: "search",
    multiple: true,
    choices: [
      { title: "TIME", value: "time" },
      { title: "Page URL", value: "pageUrl" },
      { title: "User Agent", value: "userAgent" },
      { title: "Remote IP", value: "remoteIp" },
      { title: "Credit", value: "credit" }
    ]
  },
  { name: "username", title: t("Username"), required: true },
  { name: "password", title: t("Password"), required: true }
];

type Props = {
  onClose: () => void;
};

class GmailFields extends Component<Props, {}, ContextIntegration> {
  static contextType = Context;

  render(): ReactElement {
    return <Smtp {...this.props} {...this.context} apiKeys={apiKeys} />;
  }
}

export default GmailFields;
