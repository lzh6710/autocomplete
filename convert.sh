ORIGIN_IMAGE_PATH="/home/nfs/order-sys-share-data/"
THUMB_IMAGE_PATH="/home/nfs/order-sys-share-data-thumb/"
FILE_NAME=$1

${THUMB_IMAGE_PATH}${FILE_NAME}

convert -resize '200x200!' ${ORIGIN_IMAGE_PATH}${FILE_NAME} ${THUMB_IMAGE_PATH}${FILE_NAME}
