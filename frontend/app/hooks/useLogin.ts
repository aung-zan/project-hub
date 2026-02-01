import { login } from "app/api/Auth";
import type { UserAuth } from "app/types";
import { useState } from "react";

const useLogin = () => {
  const [error, setError] = useState<Record<string, string[]>>({});
  // can add loading state.

  const userLogin = async (request: UserAuth) => {
    const data = await login(request);

    if (
      data.success === false &&
      data.message &&
      typeof data.message == "object"
    ) {
      setError(data.message);
    }

    return data;
  };

  return { userLogin, error };
};

export default useLogin;
