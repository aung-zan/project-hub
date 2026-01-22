export const getHeaders = (header: {}) => {
  return {
    "content-type": "application/json",
    accept: "application/json",
    ...header,
  };
};
