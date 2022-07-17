export const customGet = async (endpoint, emptyData) => {
  const response = await fetch(new URL(endpoint, process.env.REACT_APP_API_URL));
  const responseBody = await response.json();

  let data = emptyData;
  if (responseBody.success === 1) {
    data = responseBody.data;
  } else {
    console.log(`Fetch error: ${responseBody.error_msg}`);
  }

  return data;
}

export const customPost = async (endpoint, paramsObject) => {
  let params = new URLSearchParams(paramsObject);
  const response = await fetch(new URL(endpoint, process.env.REACT_APP_API_URL), {
    method: 'POST',
    body: params
  });
  const responseBody = await response.json();
  return responseBody;
}
