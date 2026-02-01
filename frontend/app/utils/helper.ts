import type { ConnectionConfig } from "app/types";

export const connectToServer = async (config: ConnectionConfig) => {
  const url = `${import.meta.env.VITE_APP_URL}/${config.path}`;
  const headers: Record<string, string> = {
    accept: "application/json",
  };

  if (config.token) {
    headers.authorization = "token";
  }

  const options: RequestInit = { headers, method: config.method };

  if (config.method !== "get") {
    headers["content-type"] = "application/json";
    options.body = JSON.stringify(config.data);
  }

  return await fetch(url, options);
};

export const getHeaders = (headers: Record<string, string>) => {
  return {
    "content-type": "application/json",
    accept: "application/json",
    ...headers,
  };
};
