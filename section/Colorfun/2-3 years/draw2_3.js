window.addEventListener("DOMContentLoaded", () => {
  const canvas = document.getElementById("whiteboard");
  const ctx = canvas.getContext("2d", { willReadFrequently: true });

  // State machine
  const state = {
    currentTool: "pencil",
    currentColor: "#000000",
    lineWidth: 2,
    isDrawing: false,
  };

  const tools = {
    pencil: {
      lineWidth: 2,
      cursor: "url('pencil.cur') 0 31, auto",
    },
    marker: {
      lineWidth: 10,
      cursor: "url('marker.cur') 0 31, auto",
    },
    fill: {
      cursor: "url('bucket.cur'), auto",
    },
    eraser: {
      lineWidth: 20,
      cursor: "url('eraser.cur') 0 31, auto",
    },
  };

  function updateState(newState) {
    Object.assign(state, newState);
    render();
  }

  function render() {
    // Update selected tool UI
    document
      .querySelectorAll(".tool.selectedtool")
      .forEach((btn) => btn.classList.remove("selectedtool"));
    const selectedToolButton = document.getElementById(state.currentTool);
    if (selectedToolButton) {
      selectedToolButton.classList.add("selectedtool");
    }
    const selectedColorButton = document.querySelector(
      `.color-btn[data-color='${state.currentColor}']`,
    );
    if (selectedColorButton && state.currentTool !== "eraser") {
      selectedColorButton.classList.add("selectedtool");
    }

    // Update cursor
    if (tools[state.currentTool] && tools[state.currentTool].cursor) {
      canvas.style.cursor = tools[state.currentTool].cursor;
    } else {
      canvas.style.cursor = "default";
    }

    // Update line width
    if (tools[state.currentTool] && tools[state.currentTool].lineWidth) {
      state.lineWidth = tools[state.currentTool].lineWidth;
    }
  }

  function resizeCanvas() {
    const snapshot = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const { width, height } = canvas.getBoundingClientRect();
    canvas.width = width;
    canvas.height = height;
    ctx.putImageData(snapshot, 0, 0);
  }

  // Initial setup
  resizeCanvas();
  ctx.fillStyle = "white";
  ctx.fillRect(0, 0, canvas.width, canvas.height);
  render();

  // Event Listeners
  window.addEventListener("resize", resizeCanvas);
  canvas.addEventListener("mousedown", startDrawing);
  window.addEventListener("mouseup", stopDrawing);
  canvas.addEventListener("mousemove", draw);
  canvas.addEventListener("mouseenter", (e) => {
    if (state.isDrawing) {
      const { x, y } = getMousePos(e);
      ctx.beginPath();
      ctx.moveTo(x, y);
    }
  });

  document
    .getElementById("pencil")
    .addEventListener("click", () => updateState({ currentTool: "pencil" }));
  document
    .getElementById("marker")
    .addEventListener("click", () => updateState({ currentTool: "marker" }));
  document
    .getElementById("fill")
    .addEventListener("click", () => updateState({ currentTool: "fill" }));
  document
    .getElementById("eraser")
    .addEventListener("click", () => updateState({ currentTool: "eraser" }));

  document.querySelectorAll(".color-btn").forEach((btn) => {
    btn.style.backgroundColor = btn.dataset.color;
    btn.addEventListener("click", () => {
      updateState({ currentColor: btn.dataset.color });
      if (state.currentTool === "eraser") {
        updateState({ currentTool: "pencil" });
      }
    });
  });

  document.getElementById("clear").addEventListener("click", () => {
    ctx.fillStyle = "white";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
  });

  document.getElementById("save").addEventListener("click", () => {
    // Check if a user is logged in. The 'currentUserId' variable is from your .php file.
    if (!currentUserId || currentUserId === 0) {
        alert("You must be logged in to save your drawing!");
        return;
    }

    // Get the drawing from the canvas as a Base64 string
    const imageData = canvas.toDataURL("image/png");

    const formData = new FormData();
    formData.append('user_id', currentUserId);
    formData.append('imageData', imageData);

    // Change the button text to give feedback to the user
    const saveButton = document.getElementById("save");
    saveButton.textContent = "💾 Saving...";

    fetch('../save_drawing.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log(data.message); // Log the server's response
        if (data.status === 'success') {
            saveButton.textContent = "✅ Saved!";
        } else {
            saveButton.textContent = "❌ Error";
        }
        // Reset the button text after 2 seconds
        setTimeout(() => {
            saveButton.textContent = "💾 Save";
        }, 2000);
    })
    .catch(error => {
        console.error('Error saving drawing:', error);
        saveButton.textContent = "❌ Error";
        setTimeout(() => {
            saveButton.textContent = "💾 Save";
        }, 2000);
    });
});

  function getMousePos(e) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    return {
      x: (e.clientX - rect.left) * scaleX,
      y: (e.clientY - rect.top) * scaleY,
    };
  }

  function startDrawing(e) {
    const { x, y } = getMousePos(e);
    if (state.currentTool === "fill") {
      floodFill(Math.floor(x), Math.floor(y), state.currentColor);
      return;
    }
    updateState({ isDrawing: true });
    ctx.beginPath();
    ctx.moveTo(x, y);
  }

  function stopDrawing() {
    updateState({ isDrawing: false });
    ctx.beginPath();
  }

  function draw(e) {
    if (!state.isDrawing) return;
    const { x, y } = getMousePos(e);
    ctx.lineWidth = state.lineWidth;
    ctx.lineCap = "round";
    ctx.strokeStyle =
      state.currentTool === "eraser" ? "white" : state.currentColor;
    ctx.lineTo(x, y);
    ctx.stroke();
  }

  function floodFill(startX, startY, fillColorHex) {
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const data = imageData.data;
    const targetColor = getPixelColor(startX, startY, data);
    const fillColor = hexToRgba(fillColorHex);

    if (colorsMatch(targetColor, fillColor)) return;

    const stack = [[startX, startY]];
    while (stack.length) {
      const [x, y] = stack.pop();
      if (x < 0 || x >= canvas.width || y < 0 || y >= canvas.height) continue;
      if (!colorsMatch(getPixelColor(x, y, data), targetColor)) continue;
      setPixel(x, y, fillColor, data);
      stack.push([x - 1, y]);
      stack.push([x + 1, y]);
      stack.push([x, y - 1]);
      stack.push([x, y + 1]);
    }
    ctx.putImageData(imageData, 0, 0);
  }

  function getPixelColor(x, y, data) {
    const i = (y * canvas.width + x) * 4;
    return [data[i], data[i + 1], data[i + 2], data[i + 3]];
  }

  function setPixel(x, y, rgba, data) {
    const i = (y * canvas.width + x) * 4;
    data[i] = rgba[0];
    data[i + 1] = rgba[1];
    data[i + 2] = rgba[2];
    data[i + 3] = rgba[3];
  }

  function colorsMatch(a, b) {
    return a[0] === b[0] && a[1] === b[1] && a[2] === b[2] && a[3] === b[3];
  }

  function hexToRgba(hex) {
    let c = hex.replace("#", "");
    if (c.length === 3)
      c = c
        .split("")
        .map((ch) => ch + ch)
        .join("");
    const r = parseInt(c.substring(0, 2), 16);
    const g = parseInt(c.substring(2, 4), 16);
    const b = parseInt(c.substring(4, 6), 16);
    return [r, g, b, 255];
  }
});
